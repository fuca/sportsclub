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

namespace App\Model\Service;

use \App\Services\Exceptions\NullPointerException,
	\App\Model\Entities\Event,
	\App\Model\Entities\User,
	\App\Model\Entities\SportGroup,
	\App\Services\Exceptions\DataErrorException,
    \Doctrine\ORM\NoResultException;

/**
 * Service for dealing with Event related entities
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class EventService extends \Nette\Object implements IEventService {
    
    /**
     * @var \Kdyby\Doctrine\EntityManager
     */
    private $entityManager;

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $eventDao;
    
    function __construct(\Kdyby\Doctrine\EntityManager $em) {
	$this->entityManager = $em;
	$this->eventDao = $em->getDao(Event::getClassName());
    }
    
    public function confirmParticipation(Event $e, User $u) {
	if ($e === NULL)
	    throw new NullPointerException("Argument Event was null", 0);
	if ($u === NULL)
	    throw new NullPointerException("Argument User was null", 0);
	// TODO how should this work?
    }

    public function createEvent(Event $e) {
	if ($e === NULL)
	    throw new NullPointerException("Argument Event was null", 0);
	$this->eventDao->save($e);
    }

    public function deleteEvent(Event $e) {
	if ($e === NULL)
	    throw new NullPointerException("Argument Event was null", 0);
	$db = $this->eventDao->find($e->id);
	if ($db !== NULL) { 
	    $this->eventDao->delete($db);
	} else {
	    throw new DataErrorException("Entity not found", 2);
	}
    }

    public function getEvent($id) {
	if ($id === NULL)
	    throw new NullPointerException("Argument Event was null", 0);
	$res = $this->eventDao->find($id);
	return $res;
    }

    public function getEvents(SportGroup $g) {
	if ($g === NULL) 
	    throw new NullPointerException("Argument SportGroup was null", 0);
	
	$qb = $this->entityManager->createQueryBuilder();
	$qb->select('e')
		->from('App\Model\Entities\Event', 'e')
		->innerJoin('e.groups', 'g')
		->where('g.id = :gid')
		->setParameter("gid", $g->id);
	return $qb->getQuery()->getResult();
    }

    public function rejectParticipation(Event $e, User $u) {
	if ($e === NULL)
	    throw new NullPointerException("Argument Event was null", 0);
	if ($u === NULL)
	    throw new NullPointerException("Argument User was null", 0);
	// TODO how should this work?
    }

    public function updateEvent(Event $e) {
	if ($e === NULL)
	    throw new NullPointerException("Argument Event was null", 0);
	$this->eventDao->save($e);
    }

}