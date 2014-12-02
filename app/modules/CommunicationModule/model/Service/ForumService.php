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

namespace App\CommunicationModule\Model\Service;

use \App\Model\Entities\SportGroup,
    \Grido\DataSources\Doctrine,
    \App\Model\Entities\Forum,
    \App\Model\Entities\ForumThread,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \Nette\Utils\DateTime,
    \App\Model\Entities\Comment,
    \App\SystemModule\Model\Service\ICommentable,
    \Doctrine\DBAL\DBALException,
    \Nette\Caching\Cache,
    \Doctrine\Common\Collections\ArrayCollection,
    \App\Model\Misc\Exceptions,
    \Nette\Utils\Strings,
    \Kdyby\Monolog\Logger,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Service\BaseService,
    \App\UsersModule\Model\Service\IUserService,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\SystemModule\Model\Service\ICommentService;

/**
 * Implementation of service dealing with Forum entities
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class ForumService extends BaseService implements IForumService {
    
    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $forumDao;
    
     /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $forumThreadDao;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;
    
    /**
     *
     * @var \App\SystemModule\Model\Service\ICommentService
     */
    private $commentService;

    /**
     * @var string
     */
    private $defaultImgPath;
    
    public function setCommentService(ICommentService $commentService) {
	$this->commentService = $commentService;
    }
        
    public function setDefaultImgPath($defaultImgPath) {
	$this->defaultImgPath = $defaultImgPath;
    }
    
    public function setSportGroupService (ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }
    
    public function getUserService() {
	return $this->userService;
    }

    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }
    
    function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, Forum::getClassName(), $logger);
	$this->forumDao = $em->getDao(Forum::getClassName());
	$this->forumThreadDao = $em->getDao(ForumThread::getClassName());
    }
    
    // <editor-fold desc="FORUM THREADS ADMINISTRATION">
    public function createForumThread(ForumThread $t) {
	try {
	    $this->entityManager->beginTransaction();
	    
	    $t->setUpdated(new DateTime());
	    $t->setAlias(Strings::webalize($t->getTitle()));
	    $this->authorTypeHandle($t);
	    $this->forumTypeHandle($t);
	    $this->forumThreadDao->save($t);
	    $this->entityManager->commit();
	    $this->invalidateEntityCache($t->getForum());
	    
	} catch (DBALException $ex) {
	    $this->entityManager->rollback();
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function deleteForumThread($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	
	try {
	    $db = $this->forumThreadDao->find($id);
	    if ($db !== NULL) {
		$this->forumThreadDao->delete($db);
		$this->invalidateEntityCache($db);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getForumThread($id, $useCache = true) {
	if ($id == NULL)
	    throw new Exceptions\NullPointerException("Argument Id was null");
	try {
	    if (!$useCache) {
		return $this->forumThreadDao->find($id);
	    }
	    $entityCollection = ForumThread::getClassName()."-".self::ENTITY_COLLECTION;
	    $cacheId = ForumThread::getClassName()."-{$id}";
	    $cache = $this->getEntityCache();
	    $data = $cache->load($cacheId);
	    if ($data === null) {
		$data = $this->forumThreadDao->find($id);
		$opts = [Cache::TAGS => [$entityCollection, $cacheId]];
		$cache->save($cacheId, $data, $opts);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $data;
    }
    
    public function getForumThreadAlias($alias) {
	if (empty($alias))
	    throw new Exceptions\InvalidArgumentException("Argument alias was empty");
	try {
	    $entityCollection = ForumThread::getClassName()."-".self::ENTITY_COLLECTION;
	    $id = ForumThread::getClassName()."-{$alias}";
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data == null) {
		$data = $this->forumThreadDao->createQueryBuilder("f")
		    ->where("f.alias LIKE :alias")
		    ->setParameter("alias", $alias)
		    ->getQuery()
			->getSingleResult();
		$opts = [Cache::TAGS=>[$entityCollection, $id]];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateForumThread(ForumThread $f) {
	try {
	    $fDb = $this->forumThreadDao->find($f->getId());
	    if ($fDb !== null) {
		$fDb->fromArray($f->toArray());
		$fDb->setUpdated(new DateTime());
		$this->editorTypeHandle($fDb);
		$this->authorTypeHandle($fDb);
		$fDb->setAlias(Strings::webalize($f->getTitle()));
		$this->forumTypeHandle($fDb);
		
		$this->entityManager->merge($fDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($fDb);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getForumThreadsDataSource() {
	$model = new Doctrine(
		$this->forumThreadDao->createQueryBuilder('ft'));
	return $model;
    }
    
    private function forumTypeHandle(ForumThread $t) {
	try {
	    $forum = null;
	    $id = $this->getMixId($t->getForum());
	    if ($id !== null) 
		$forum = $this->forumDao->find($id);
	    $t->setForum($forum);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    // <editor-fold desc="FORUM ADMINISTRATION">
    public function createForum(Forum $f) {
	try {
	    $this->entityManager->beginTransaction();
	    $f->setUpdated(new DateTime());
	    $f->setAuthor($f->getEditor());
	    $this->sportGroupsTypeHandle($f);
	    if (empty($f->getImgName())) {
		$f->setImgName($this->defaultImgPath);
	    }
	    $f->setAlias(Strings::webalize($f->getTitle()));
	    
	    $this->forumDao->save($f);
	    $this->entityManager->commit();
	} catch (DBALException $ex) {
	    $this->entityManager->rollback();
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteForum($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	
	try {
	    $db = $this->forumDao->find($id);
	    if ($db !== NULL) {
		$this->forumDao->delete($db);
		$this->invalidateEntityCache();
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getForum($id, $useCache = true) {
	if ($id == NULL)
	    throw new Exceptions\NullPointerException("Argument Id was null", 0);
	try {
	    if (!$useCache) {
		return $this->forumDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->forumDao->find($id);
		$opts = [Cache::TAGS => [self::ENTITY_COLLECTION, self::SELECT_COLLECTION, $id]];
		$cache->save($id, $data, $opts);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $data;
    }
    
    public function getForumAlias($alias) {
	if (empty($alias))
	    throw new Exceptions\InvalidArgumentException("Argument alias was empty");
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load($alias);
	    if ($data == null) {
		$data = $this->forumDao->createQueryBuilder("f")
		    ->where("f.alias LIKE :alias")
		    ->setParameter("alias", $alias)
		    ->getQuery()->getSingleResult();
		$opts = [Cache::TAGS=>[self::ENTITY_COLLECTION, $alias, self::SELECT_COLLECTION]];
		$cache->save($alias, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getForums(SportGroup $g) {
	try {
	    if ($g == null) {
		return $this->forumDao->findAll();
	    }
	    $qb = $this->entityManager->createQueryBuilder();
	    $qb->select('f')
		    ->from('App\Model\Entities\Forum', 'f')
		    ->innerJoin('f.groups', 'g')
		    ->where('g.id = :gid')
		    ->setParameter("gid", $g->id);
	    return $qb->getQuery()->getResult();
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getSelectForums($id = null) {
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load(self::SELECT_COLLECTION);
	    if ($data === null) {
		$data = $this->forumDao->findPairs([], "title");
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    if ($id != null) {
		unset($data[$id]);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateForum(Forum $f) {
	if ($f === NULL)
	    throw new Exceptions\NullPointerException("Argument Forum was null");
	try {
	    $fDb = $this->forumDao->find($f->getId());
	    if ($fDb !== null) {
		$fDb->fromArray($f->toArray());
		$fDb->setUpdated(new DateTime());
		$this->sportGroupsTypeHandle($fDb);
		$this->editorTypeHandle($fDb);
		$this->authorTypeHandle($fDb);
		$fDb->setAlias(Strings::webalize($f->getTitle()));
		$this->entityManager->merge($fDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($fDb);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getForumDataSource() {
	$model = new Doctrine(
		$this->forumDao->createQueryBuilder('f'));
	return $model;
    }
    
    private function sportGroupsTypeHandle(Forum $e) {
	if ($e === null)
	    throw new Exceptions\NullPointerException("Argument event was null");
	try {
	    $coll = new ArrayCollection();
	    foreach ($e->getGroups() as $eg) {
	    $dbG = $this->sportGroupService->getSportGroup($eg, false);
		if ($dbG !== null) {
		   $coll->add($dbG); 
		}
	    }
	    $e->setGroups($coll);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $e;
    }
    
    private function editorTypeHandle(BaseEntity $e) {
	if ($e === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null");
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($e->getEditor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $e->setEditor($editor);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function authorTypeHandle(BaseEntity $e) {
	if ($e === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null");
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($e->getAuthor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $e->setAuthor($editor);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    // </editor-fold>
    public function createComment(Comment $c, ICommentable $e) {
	$now = new DateTime();
	try {
	    $this->entityManager->beginTransaction();
	    $wpDb = $this->forumThreadDao->find($e->getId());
	    if ($wpDb !== null) {
		//$this->commentService->createComment($c);
		$ccs = $wpDb->getComments();
		//$ccs->clear(); // vymaze celou kolekci
		$ccs->add($c);
		$wpDb->setLastActivity($now);
		$wpDb->getForum()->setLastActivity($now);
		$this->entityManager->merge($wpDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($wpDb);
	    }
	    $this->entityManager->commit();
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateComment(Comment $c, ICommentable $e) {
	try {
	    $this->entityManager->beginTransaction();
	    //$wpDb = $this->wallDao->find($e->getId());
	    $this->commentService->updateComment($c);
	    $this->invalidateEntityCache($e);
	    $this->entityManager->commit();
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteComment(Comment $c, ICommentable $e) {
	try {
	    $wpDb = $this->forumThreadDao->find($e->getId());
	    if ($wpDb !== null) {
		$coll = $wpDb->getComments();
		$id = $c->getId();
		$comment = $coll->filter(function ($e) use ($id) {return $e->getId() == $id;})->first();
		$index = $coll->indexOf($comment);
		$coll->remove($index);

		$this->entityManager->merge($wpDb);
		$this->entityManager->flush($wpDb);
		$this->commentService->deleteComment($c->getId());    
		$this->invalidateEntityCache($wpDb);
	    }
	} catch (Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
}
