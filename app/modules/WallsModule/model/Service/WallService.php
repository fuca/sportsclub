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

namespace App\WallsModule\Model\Service;

use \Nette\Diagnostics\Debugger,
    \App\Model\Entities\WallPost,
    \App\Model\Entities\SportGroup,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \Grido\DataSources\Doctrine,
    \App\Model\Misc\Exceptions,
    \Doctrine\Common\Collections\ArrayCollection,
    \Nette\Utils\DateTime,
    \Nette\Caching\Cache,
    \App\SystemModule\Model\Service\ICommentable,
    \App\Model\Entities\Comment,
    \App\UsersModule\Model\Service\IUserService,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\Services\Exceptions\NullPointerException,
    \App\SystemModule\Model\Service\ICommentService;

/**
 * Description of WallService
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class WallService extends BaseService implements IWallService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $wallDao;

    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;

    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;

    /**
     * @var \App\SystemModule\Model\Service\ICommentService
     */
    public $commentService;

    public function setCommentService(ICommentService $commentService) {
	$this->commentService = $commentService;
    }

    public function setSportGroupService(ISportGroupService $sgs) {
	$this->sportGroupService = $sgs;
    }

    public function setUserService(IUserService $us) {
	$this->userService = $us;
    }

    public function getUserService() {
	return $this->userService;
    }

    public function __construct(EntityManager $em) {
	parent::__construct($em, WallPost::getClassName());
	$this->wallDao = $em->getDao(WallPost::getClassName());
    }

    public function createWallPost(WallPost $w) {
	if ($w == null)
	    throw new Exceptions\NullPointerException("Argument WallPost cannot be null", 0);
	try {
	    $w->setAuthor($w->getEditor());
	    $w->setUpdated(new DateTime());
	    $this->sportGroupsTypeHandle($w);
	    $this->wallDao->save($w);
	    $this->invalidateEntityCache();
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getWallPost($id, $useCache = true) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given", 1);
	try {
	    if (!$useCache) {
		return $this->wallDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->wallDao->find($id);
		$opts = [Cache::TAGS => [self::ENTITY_COLLECTION, self::SELECT_COLLECTION, $id]];
		$cache->save($id, $data, $opts);
	    }
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $data;
    }

    public function getWallPosts(SportGroup $g = null) {
	try {
	    if (empty($g)) {
		return $this->wallDao->findAll();
	    } else {
		$qb = $this->entityManager->createQueryBuilder();
		$qb->select('w')
			->from('App\Model\Entities\WallPost', 'w')
			->innerJoin('e.groups', 'g')
			->where('g.id = :gid')
			->setParameter("gid", $g->id);
		return $qb->getQuery()->getResult();
	    }
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function removeWallPost($id) {
	if ($id == null)
	    throw new Exceptions\NullPointerException("Argument WallPost cannot be null", 0);
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException();
	try {
	    $wpDb = $this->wallDao->find($id);
	    if ($wpDb !== null) {
		$this->wallDao->delete($wpDb);
	    }
	    $this->invalidateEntityCache($wpDb);
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateWallPost(WallPost $w) {
	if ($w == null)
	    throw new Exceptions\NullPointerException("Argument WallPost cannot be null", 0);
	try {
	    $this->entityManager->beginTransaction();
	    $wpDb = $this->wallDao->find($w->getId());
	    if ($wpDb !== null) {
		$wpDb->fromArray($w->toArray());
		$this->sportGroupsTypeHandle($wpDb);
		$wpDb->setUpdated(new DateTime());
		$this->authorTypeHandle($wpDb);
		$this->editorTypeHandle($wpDb);
		$this->entityManager->merge($wpDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($wpDb);
	    }
	    $this->entityManager->commit();
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $wpDb;
    }

    public function getWallPostsDatasource() {
	$model = new Doctrine(
		$this->wallDao->createQueryBuilder('wp'));
	return $model;
    }

    private function sportGroupsTypeHandle(WallPost $wp) {
	if ($wp === null)
	    throw new Exceptions\NullPointerException("Argument event was null");
	try {
	    $coll = new ArrayCollection();
	    foreach ($wp->getGroups() as $wpg) {
		$dbG = $this->sportGroupService->getSportGroup($wpg, false);
		if ($dbG !== null) {
		    $coll->add($dbG);
		}
	    }
	    $wp->setGroups($coll);
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	return $wp;
    }

    private function editorTypeHandle(WallPost $a) {
	if ($a === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null", 0);
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($a->getEditor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $a->setEditor($editor);
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex);
	}
    }

    private function authorTypeHandle(WallPost $a) {
	if ($a === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null", 0);
	try {
	    $author = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($a->getAuthor());
		if ($id !== null)
		    $author = $this->getUserService()->getUser($id, false);
	    }
	    $a->setAuthor($author);
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex);
	}
    }

    public function createComment(Comment $c, ICommentable $e) {
	try {
	    $this->entityManager->beginTransaction();
	    $wpDb = $this->wallDao->find($e->getId());
	    if ($wpDb !== null) {
		//$this->commentService->createComment($c);
		$ccs = $wpDb->getComments();
		//$ccs->clear(); // vymaze celou kolekci
		$ccs->add($c);
		$this->entityManager->merge($wpDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($wpDb);
	    }
	    $this->entityManager->commit();
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->getLogger()->addError($ex->getMessage());
	    throw new Exceptions\DataErrorException("Error occured while adding comment");
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
	    $this->getLogger()->addError($ex->getMessage());
	    throw new Exceptions\DataErrorException("Error occured while adding comment");
	}
    }

    public function deleteComment(Comment $c, ICommentable $e) {
	try {
	    $wpDb = $this->wallDao->find($e->getId());
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
	    
	}
	
    }

}
