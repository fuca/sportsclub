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
    App\Model\Entities\EventParticipation,
    \App\Model\Entities\User,
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
class EventParticipationService extends BaseService implements IEventParticipationService {

    function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, EventParticipation::getClassName(), $logger);
	$this->eventDao = $em->getDao(EventParticipation::getClassName());
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

    public function createParticipation(EventParticipation $e) {
	
    }

    public function updateParticipation(EventParticipation $e) {
	
    }
}
