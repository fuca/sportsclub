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
    
    function createEvent(Event $e);
    
    function updateEvent(Event $e);
    
    function deleteEvent($id);
    
    function getEvent($id);
    
    function getUserEventsDataSource(User $u);
    
    function getEventAlias($alias);
    
    function getEvents(SportGroup $g);
    
    function getEventsDataSource();
    
    function createEventParticipation(EventParticipation $ep);
    
    function deleteEventParticipation($u, Event $e);
}
