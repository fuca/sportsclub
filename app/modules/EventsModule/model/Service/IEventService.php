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
    \App\Model\Entities\SportGroup,
    \App\Model\Entities\User,
    \App\Model\Entities\EventParticipation;

/**
 * Interface for Event service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IEventService {

    /**
     * @param Event $e
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createEvent(Event $e);

    /**
     * @param Event $e
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updateEvent(Event $e);

    /**
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteEvent($id);

    /**
     * @param numeric $id
     * @param boolean $useCache
     * @return Event
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getEvent($id);

    /**
     * @param User $u
     * @return Doctrine
     */
    function getUserEventsDataSource(User $u);

    /**
     * 
     * @param string $alias
     * @return Event
     * @throws Exceptions\InvalidStateException
     * @throws Exceptions\DataErrorException
     */
    function getEventAlias($alias);

    /**
     * @param SportGroup $g
     * @return array
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function getEvents(SportGroup $g);

    /**
     * @return Doctrine
     */
    function getEventsDataSource();

    /**
     * @param EventParticipation $ep
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createEventParticipation(EventParticipation $ep);

    /**
     * @param User|numeric $u
     * @param Event $e
     * @throws Exceptions\DataErrorException
     */
    function deleteEventParticipation($u, Event $e);

    /**
     * @param numeric $id
     * @return EventParticipation
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getEventParticipation($id);
}
