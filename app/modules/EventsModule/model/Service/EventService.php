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

namespace App\EventsModule\Model\Service;

use \App\Model\Entities\Event,
    \App\Model\Entities\User,
    \App\Model\Entities\EventParticipation,
    \App\Model\Entities\SportGroup,
    \App\Model\Misc\Exceptions,
    \Kdyby\Doctrine\DuplicateEntryException,
    \Kdyby\Doctrine\DBALException,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Service\BaseService,
    \Nette\Utils\DateTime,
    \Nette\Utils\Strings,
    \Nette\Caching\Cache,
    \EventCalendar\IEventModel,
    \App\Model\Entities\Comment,
    \App\SystemModule\Model\Service\ICommentable,
    \Kdyby\Monolog\Logger,
    \Grido\DataSources\Doctrine,
    \Doctrine\Common\Collections\ArrayCollection,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\UsersModule\Model\Service\IUserService,
    \App\SystemModule\Model\Service\ICommentService;

/**
 * Service for dealing with Event related entities
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class EventService extends BaseService implements IEventService, IEventModel {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $eventDao;
    
    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $participationDao;
    
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
    
    /** @var Event dispatched every time after create of Event, EventParticipation */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of Event, EventParticipation */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of Event, EventParticipation */
    public $onDelete = [];
    
    public function getUserService() {
	return $this->userService;
    }
    
    public function setGroupService(ISportGroupService $egs) {
	$this->sportGroupService = $egs;
    }
    
    public function setUserService(IUserService $uss) {
	$this->userService = $uss;
    }
    
    public function setCommentService(ICommentService $cs) {
	$this->commentService = $cs;
    }

    function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, Event::getClassName(), $logger);
	$this->eventDao = $em->getDao(Event::getClassName());
	$this->participationDao = $em->getDao(EventParticipation::getClassName());
    }
    
    public function createEventParticipation(EventParticipation $ep) {
	try {
	    $eDb = $this->getEvent($ep->getEvent()->getId(), false);
	    if ($eDb !== null) {
		$ep->setEvent($eDb);
		$this->ownerTypeHandle($ep);
		$this->participationDao->save($ep);
		$this->invalidateEntityCache($ep->getEvent());
		$this->onCreate($eDb);
	    }
	} catch (DBALException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException("Event with this title already exist");
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function deleteEventParticipation($u, Event $e) {
	try {
	    $eDb = $this->getEvent($e->getId());
	    $uDb = $this->userService->getUser($this->getMixId($u));
	    if ($eDb !== null && $eDb !== null) {
		$pDb = $this->participationDao->createQueryBuilder("p")
			->where("p.event = :event")
			->andWhere("p.owner = :owner")
			->setParameter("event", $eDb->getId())
			->setParameter("owner", $uDb->getId())
			->getQuery()
			->getSingleResult();
		if ($pDb !== null) {
		    $this->participationDao->delete($pDb);
		    $this->onDelete($pDb);
		}
	    }
	    $this->invalidateEntityCache($eDb);
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getEventParticipation($id) {
	if (!is_numeric($id)) 
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    return $this->participationDao->find($id);
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function createEvent(Event $e) {
	if ($e === NULL)
	    throw new Exceptions\NullPointerException("Argument Event was null");
	try {
	    $e->setEditor($e->getAuthor());
	    $e->setUpdated(new DateTime);
	    $e->setAlias(Strings::webalize($e->getTitle()));
	    $this->sportGroupsTypeHandle($e);
	    $this->eventDao->save($e);
	    $this->invalidateEntityCache($e);
	    $this->onCreate($e);
	} catch (DBALException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException("Event with this title already exist");
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
   
    private function sportGroupsTypeHandle(Event $e) {
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
	} catch (\Exception $e) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $e;
    }
    
    private function editorTypeHandle(Event $e) {
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
    
    private function ownerTypeHandle(EventParticipation $e) {
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($e->getOwner());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $e->setOwner($editor);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function authorTypeHandle(Event $e) {
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
     

    public function deleteEvent($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    $dbE = $this->eventDao->find($id);
	    if ($dbE !== null) {
		$this->eventDao->delete($dbE);
		$this->onDelete($dbE);
	    }
	} catch (DBALException $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getEvent($id, $useCache = true) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    if (!$useCache) {
		return $this->eventDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->eventDao->find($id);
		$opt = [Cache::TAGS => [self::ENTITY_COLLECTION, self::SELECT_COLLECTION, $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $data;
    }
    
    public function getEventAlias($alias) {
	if (empty($alias)) 
	    throw new Exceptions\InvalidStateException("Argument alias has to be non empty string");
	try {
	    return $this->eventDao->createQueryBuilder("e")
		    ->where("e.alias like :alias")
		    ->setParameter("alias", $alias)
		    ->getQuery()->getSingleResult();
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getEvents(SportGroup $g) {
	if ($g === NULL)
	    throw new Exceptions\NullPointerException("Argument SportGroup was null");
	
	try {
	    $qb = $this->entityManager->createQueryBuilder();
	    $qb->select('e')
		    ->from('App\Model\Entities\Event', 'e')
		    ->innerJoin('e.groups', 'g')
		    ->where('g.id = :gid')
		    ->setParameter("gid", $g->id)
		    ->andWhere("e.takePlaceTill >= :now")
		    ->setParameter("now", new DateTime());
	    return $qb->getQuery()->getResult();
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateEvent(Event $e) {
	if ($e === NULL)
	    throw new Exceptions\NullPointerException("Argument Event was null");
	try {
	    $this->entityManager->beginTransaction();
	    $eDb = $this->eventDao->find($e->getId());
	    if ($eDb !== null) {
		$eDb->fromArray($e->toArray());
		$this->sportGroupsTypeHandle($eDb);
		$this->editorTypeHandle($eDb);
		$this->authorTypeHandle($eDb);
		$eDb->setUpdated(new DateTime());
		$this->entityManager->merge($eDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($eDb);
		$this->onUpdate($eDb);
	    }
	    $this->entityManager->commit();
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getEventsDataSource() {
	$model = new Doctrine(
		$this->eventDao->createQueryBuilder('ev'));
	return $model;
    }

    public function getForDate($year, $month, $day, array $modifier = []) {
	$abbr = null;
	$date = new DateTime();
	$date->setDate($year, $month, $day);
	if (!empty($modifier) && isset($modifier["abbr"])) {
	    $abbr = $modifier["abbr"];
	}
	try {
	    $qb = $this->eventDao->createQueryBuilder("e");
	    
	    if ($abbr !== null) {
		
//		    $qb->from('App\Model\Entities\Event', 'e')
		    $qb->join("e.groups", "g")
			->where("g.abbr = :abbr")
			->setParameter("abbr", $abbr);
	    }
	    $qq = $qb->andWhere("e.takePlaceSince <= :now")
		    ->andWhere("e.takePlaceTill >= :now")
		    ->setParameter("now", $date)
		    ->getQuery();
	    $res = $qq->getResult();
	   
	    return $res;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function isForDate($year, $month, $day, array $modifier = []) {
	$res = $this->getForDate($year, $month, $day, $modifier);
	return count($res) > 0?true:false;
    }
    
    
    
    public function createComment(Comment $c, ICommentable $e) {
	try {
	    $this->entityManager->beginTransaction();
	    $wpDb = $this->eventDao->find($e->getId());
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
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateComment(Comment $c, ICommentable $e) {
	try {
	    $this->entityManager->beginTransaction();
	    //$wpDb = $this->eventDao->find($e->getId());
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
	    $wpDb = $this->eventDao->find($e->getId());
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
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	
    }
    
    public function getUserEventsDatasource(User $u) {
	$model = new Doctrine(
//		$this->participationDao->createQueryBuilder("ep")
//		->where("ep.owner = :owner")
//		->setParameter("owner", $u->getId()));
		
		$this->participationDao->createQueryBuilder()
		->select("ep, ev.title AS title, ev.takePlaceSince as takePlaceSince, ev.takePlaceTill AS takePlaceTill, ev.eventType AS eventType")
		->add("from", EventParticipation::getClassName()." ep LEFT JOIN ep.event ev")
		->where("ep.owner = :owner")
		->setParameter("owner", $u->getId()));
	return $model;
    }

}
