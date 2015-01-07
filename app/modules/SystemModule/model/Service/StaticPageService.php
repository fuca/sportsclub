<?php

/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\SystemModule\Model\Service;

use \App\Model\Entities\StaticPage,
    \App\Model\Entities\SportGroup,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Misc\Exceptions,
    \Kdyby\Doctrine\DBALException,
    \App\SystemModule\Model\Service\ICommentable,
    \App\Model\Entities\Comment,
    \Nette\Caching\Cache,
    \Kdyby\Monolog\Logger,
    \Nette\Utils\DateTime,
    \Nette\Utils\Strings,
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\UsersModule\Model\Service\IUserService,
    \App\SystemModule\Model\Service\ICommentService;

/**
 * Service for managing system resources
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
final class StaticPageService extends BaseService implements IStaticPageService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $pageDao;
    
    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $sportGroupDao;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    /**
     * Comment service
     * @var \App\SystemModule\Model\Service\ICommentService
     */
    private $commentService;
    
    /**
     * Array of event callbacks
     * @var array Array of callbacks
     */
    public $onCreate = [];
    
    /**
     * Array of event callbacks
     * @var array Array of callbacks
     */
    public $onDelete = [];
    
    /**
     * Array of event callbacks
     * @var array Array of callbacks
     */
    public $onUpdate = [];
    
    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, StaticPage::getClassName(), $logger);
	$this->pageDao = $em->getDao(StaticPage::getClassName());
	$this->sportGroupDao = $em->getDao(SportGroup::getClassName());
    }
    
    public function getCommentService() {
	return $this->commentService;
    }

    public function setCommentService(ICommentService $commentService) {
	$this->commentService = $commentService;
    }

    
    function getUserService() {
	return $this->userService;
    }

    function setSportGroupService(ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }

    function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }
    
    public function createStaticPage(StaticPage $sp) {
	try {
	    $this->entityManager->beginTransaction();
	    
	    $sp->setUpdated(new DateTime());
	    $this->sportGroupTypeHandle($sp);	   
	    $this->handleAbbrConsistency($sp);
	    $this->editorTypeHandle($sp);
	    
	    $this->pageDao->save($sp);
	    $this->entityManager->commit();
	    $this->invalidateEntityCache($sp);
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	$this->onCreate(clone $sp);
    }

    private function handleAbbrConsistency(StaticPage $sp, $checkOnly = false) {
	if (!$checkOnly) {
	    $title = Strings::webalize($sp->getTitle());
	    $abbr = $title."-{$sp->getGroup()->getAbbr()}";
	    $sp->setAbbr($abbr);
	} else {
	    $sp->setAbbr(Strings::webalize($sp->getAbbr()));
	}
    }
    
    private function editorTypeHandle(StaticPage $sp) {
	try {
	    $editor = null;
	    $id = $this->getMixId($sp->getEditor());
	    if ($id !== null) {
		$editor = $this->getUserService()->getUser($id, false);
	    }
	    $sp->setEditor($editor);
	    return $sp;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    
    private function sportGroupTypeHandle(StaticPage $sp) {
	try {
	    $group = null;
	    $id = $this->getMixId($sp->getGroup());
	    if ($id !== null) {
		$group = $this->sportGroupDao->find($id);
	    }
	    $sp->setGroup($group);
	    return $sp;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    
    public function deleteStaticPage($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    $db = $this->pageDao->find($id);
	    if ($db !== null) {
		$this->onDelete(clone $db);
		$this->pageDao->delete($db);
	    }
	    $this->invalidateEntityCache($db);
	} catch (DBALException $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DependencyException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getStaticPage($id, $useCache = true) {
	if (!is_numeric($id))
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, $id given");
	try {
	    if (!$useCache) {
		return $this->pageDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);

	    if (empty($data)) {
		$data = $this->pageDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), 
					self::ENTITY_COLLECTION, 
					self::SELECT_COLLECTION, 
					$id]];
		$cache->save($id, $data, $opt);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    function getStaticPageAbbr($abbr, $useCache = true) {
	try {
	    if (!$useCache) {
		return $this->pageDao->findBy(["abbr"=>$abbr]);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($abbr);

	    if (empty($data)) {
		$data = $this->pageDao->findBy(["abbr"=>$abbr])[0]; // abbr is unique
		$opt = [Cache::TAGS => [$this->getEntityClassName(), 
					self::ENTITY_COLLECTION, 
					self::SELECT_COLLECTION, 
					$abbr, $data->getId()]];
		$cache->save($abbr, $data, $opt);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getPagesDataSource() {
	$model = new \Grido\DataSources\Doctrine(
		$this->pageDao->createQueryBuilder("p"));
	return $model;
    }

    public function updateStaticPage(StaticPage $sp) {
	try {
	    $dbPage = $this->pageDao->find($sp->getId());
	    if ($dbPage !== null) {
		$dbPage->fromArray($sp->toArray());
		
		$dbPage->setUpdated(new DateTime());
		$this->sportGroupTypeHandle($dbPage);
		$this->handleAbbrConsistency($dbPage, true);
		$this->editorTypeHandle($dbPage);
		
		$this->entityManager->merge($dbPage);
		$this->entityManager->flush();
		$this->invalidateEntityCache($dbPage);
	    }
	    $this->onUpdate(clone $dbPage);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex->getMessage());
	    throw new Exceptions\DuplicateEntryException($ex);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSelectStaticPages($useCache = true) {
	try {
	    if (!$useCache) {
		return $data = $this->pageDao->findPairs([], "title");
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load(self::SELECT_COLLECTION);
	    if ($data === null) {
		$data = $this->pageDao->findPairs([], "title");
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getGroupStaticPages(SportGroup $g) {
	try {
	    $q = $this->pageDao->createQueryBuilder("sp")
		    ->where("sp.group = :group")
		    ->setParameter("group", $g->getId())
		    ->getQuery();
	    return $q->getResult();
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function createComment(Comment $c, ICommentable $e) {
	try {
	    $wpDb = $this->pageDao->find($e->getId());
	    if ($wpDb !== null) {
		$ccs = $wpDb->getComments();
		$ccs->add($c);
		$this->entityManager->merge($wpDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($wpDb);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateComment(Comment $c, ICommentable $e) {
	try {
	    $this->commentService->updateComment($c);
	    $this->invalidateEntityCache($e);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteComment($c, ICommentable $e) {
	try {
	    $wpDb = $this->pageDao->find($e->getId());
	    if ($wpDb !== null) {
		$coll = $wpDb->getComments();
		$id = $this->getMixId($c);
		$comment = $coll->filter(function ($e) use ($id) {return $e->getId() == $id;})->first();
		$index = $coll->indexOf($comment);
		if (!is_numeric($index)) return;
		$coll->remove($index);

		$this->entityManager->merge($wpDb);
		$this->entityManager->flush($wpDb);
		$this->commentService->deleteComment($id);    
		$this->invalidateEntityCache($wpDb);
	    }
	} catch (Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
}
