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
    \App\Model\Entities\SportGroup,
    \App\Services\Exceptions,
    \Kdyby\Doctrine\DuplicateEntryException,
    \Doctrine\ORM\NoResultException,
    \Kdyby\Doctrine\DBALException,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Service\BaseService,
    \Nette\Utils\DateTime,
    \Nette\Utils\Strings,
    \Nette\Caching\Cache,
    \Grido\DataSources\Doctrine,
    \Doctrine\Common\Collections\ArrayCollection,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\UsersModule\Model\Service\IUserService;

/**
 * Service for dealing with Event related entities
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class EventService extends BaseService implements IEventService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $eventDao;
    
    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    public function getUserService() {
	return $this->userService;
    }
    
    public function setGroupService(ISportGroupService $egs) {
	$this->sportGroupService = $egs;
    }
    
    public function setUserService(IUserService $uss) {
	$this->userService = $uss;
    }

    function __construct(EntityManager $em) {
	parent::__construct($em, Event::getClassName());
	$this->eventDao = $em->getDao(Event::getClassName());
    }

    public function confirmParticipation(Event $e, User $u) {
	if ($e === NULL)
	    throw new Exceptions\NullPointerException("Argument Event was null", 0);
	if ($u === NULL)
	    throw new Exceptions\NullPointerException("Argument User was null", 0);
	// TODO how should this work?
	// dodelat entitu nebo vazebni tabulku participaci? asi primo entitu, at pak muzu udelat control pro vypis tech participaci k dane evente
    }
    
    public function rejectParticipation(Event $e, User $u) {
	if ($e === NULL)
	    throw new Exceptions\NullPointerException("Argument Event was null", 0);
	if ($u === NULL)
	    throw new Exceptions\NullPointerException("Argument User was null", 0);
	// TODO how should this work?
    }

    public function createEvent(Event $e) {
	if ($e === NULL)
	    throw new Exceptions\NullPointerException("Argument Event was null", 0);
	try {
	    $e->setEditor($e->getAuthor());
	    $e->setUpdated(new DateTime);
	    $e->setAlias(Strings::webalize($e->getTitle()));
	    $this->sportGroupsTypeHandle($e);
	    $this->eventDao->save($e);
	    $this->invalidateEntityCache($e);
	} catch (\Kdyby\Doctrine\DBALException $ex) {
	    dd($ex);
	    //throw new Exceptions\DuplicateEntryException("Event with this title already exists");
	} catch (Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage());
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
	} catch (Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	return $e;
    }
    
    private function editorTypeHandle(Event $e) {
	if ($e === null)
	    throw new NullPointerException("Argument Event cannot be null", 0);
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($e->getEditor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $e->setEditor($editor);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }
    
    private function authorTypeHandle(Event $e) {
	if ($e === null)
	    throw new NullPointerException("Argument Event cannot be null", 0);
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($e->getAuthor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $e->setAuthor($editor);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }
     

    public function deleteEvent($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    $dbE = $this->eventDao->find($id);
	    if ($dbE !== null) {
		$this->eventDao->delete($dbE);
	    }
	} catch (DBALException $ex) {
	    $this->getLogger()->addError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), 0, $ex->getPrevious());
	}
    }

    public function getEvent($id, $useCache = true) {
	if ($id === NULL)
	    throw new Exceptions\NullPointerException("Argument Id was null", 0);
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric", 1);
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
	    throw new DataErrorException($ex);
	}
	return $data;
    }

    public function getEvents(SportGroup $g) {
	if ($g === NULL)
	    throw new Exceptions\NullPointerException("Argument SportGroup was null", 0);

	$qb = $this->entityManager->createQueryBuilder();
	$qb->select('e')
		->from('App\Model\Entities\Event', 'e')
		->innerJoin('e.groups', 'g')
		->where('g.id = :gid')
		->setParameter("gid", $g->id);
	return $qb->getQuery()->getResult();
    }

    public function updateEvent(Event $e) {
	if ($e === NULL)
	    throw new Exceptions\NullPointerException("Argument Event was null", 0);
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
	    }
	    $this->entityManager->commit();
	} catch (DuplicateEntryException $ex) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException($ex);
	} catch (Exception $ex) {
	    throw new Exceptions\DataErrorException("Update event could not been proceeded");
	}
    }

    public function getEventsDataSource() {
	$model = new Doctrine(
		$this->eventDao->createQueryBuilder('ev'));
	return $model;
    }
}
