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

use \App\Model\Entities\MailBoxEntry;

/**
 * Interface for PrivateMessageService
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
interface IPrivateMessageService {

    /**
     * @param MailBoxEntry $pm
     * @throws Exceptions\DataErrorException
     */
    function createEntry(MailBoxEntry $pm);

    /**
     * @param numeric $id
     * @param boolean $useCache
     * @return MailBoxEntry
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getEntry($id);

    /**
     * @param numeric $id
     * @throws Exceptions\DataErrorException
     */
    function deleteEntry($id);

    /**
     * Mark MailBoxEntry with given id as read
     * @param numeric $id
     */
    function markAsRead($id);

    /**
     * Mark MailBoxEntry with given id as unread
     * @param numeric $id
     */
    function markAsUnread($id);

    /**
     * Mark MailBoxEntry with given id as deleted
     * @param numeric $id
     */
    function markAsDeleted($id);

    /**
     * Returns count of unread messages
     * @param numeric $user
     * @return numeric
     * @throws Exceptions\DataErrorException
     */
    function getNewsCount($user);

    /**
     * @param User|numeric $user
     * @return Doctrine
     */
    function getInboxDatasource($user);

    /**
     * @param User|numeric $user
     * @return Doctrine
     */
    function getOutboxDatasource($user);

    /**
     * @param User|numeric $user
     * @return Doctrine
     */
    function getDeletedDatasource($user);
    
    /**
     * Toggles star markdown at message entry
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    public function starToggle($id);
}
