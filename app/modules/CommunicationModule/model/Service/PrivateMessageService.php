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

use \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \Kdyby\Monolog\Logger,
    \Grido\DataSources\Doctrine,
    \Nette\Caching\Cache,
    \Nette\Utils\DateTime,
    \App\Model\Entities\User,
    \App\Model\Misc\Exceptions,
    \App\Model\Entities\PrivateMessage,
    \App\Model\Entities\MailBoxEntry,
    \App\Model\Misc\Enum\MailBoxEntryType,
    \App\CommunicationModule\Model\Service\IPrivateMessageService,
    \App\UsersModule\Model\Service\IUserService,
    \App\SystemModule\Model\Service\ISportGroupService;

/**
 * Description of MessagesService
 *
 * @author fuca
 */
final class PrivateMessageService extends BaseService implements IPrivateMessageService {

    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;

    /**
     * @var Kdyby\Doctrine\EntityDao
     */
    private $messageDao;

    /**
     * @var Kdyby\Doctrine\EntityDao
     */
    private $mailboxDao;
    
    /** @var Event dispatched every time after create of MailboxEntry */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of MailboxEntry */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of MailboxEntry */
    public $onDelete = [];

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, MailBoxEntry::getClassName(), $logger);
	$this->messageDao = $this->entityManager->getDao(PrivateMessage::getClassName());
	$this->mailboxDao = $this->entityManager->getDao(MailBoxEntry::getClassName());
    }
    
    public function getUserService() {
	return $this->userService;
    }

    public function getMessageDao() {
	return $this->messageDao;
    }

    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }

    public function setMessageDao(EntityDao $messageDao) {
	$this->messageDao = $messageDao;
    }

    public function createEntry(MailBoxEntry $mb) {
	try {
	    foreach ($mb->getRecipient() as $toId) {
		$mb->setRecipient($toId);
		$this->saveMailboxEntry($mb);
	    }
	    $mb->setOwner($mb->getSender());
	    $mb->setRecipient($mb->getOwner());
	    $mb->setType(MailBoxEntryType::READ);
	    $this->saveMailboxEntry($mb);
	    $this->onCreate($mb);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		$ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function saveMailboxEntry(MailBoxEntry $mb) {	
	$toObject = $this->userService->getUser($this->getMixId($mb->getRecipient()), false);
	$fromObject = $this->userService->getUser($this->getMixId($mb->getSender()), false);
	$mb->setOwner($toObject);
	$mb->setRecipient($toObject);
	$mb->setSender($fromObject);	
	$this->mailboxDao->save($mb);
    }
    
    public function getEntry($id, $useCache = true) {
	if (!is_numeric($id)) 
	    throw new Exceptions\InvalidArgumentException("Argument id must be type of numeric, '$id' given");
	try {	    
	    //$qb = $this->mailboxDao->createQueryBuilder("mb")->where("mb.owner = :owner")->andWhere("mb.message = :message")->setParameter("owner", $uId)->setParameter("message", $id);
	    
	    if (!$useCache) {
		//return $qb->getQuery()->getSingleResult();
		return $this->mailboxDao->find($id);
	    }
	    
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->mailboxDao->find($id);
		$opts = [Cache::TAGS => [$id]];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getRecipients($id) {
	try {
	    return $this->mailboxDao->createQueryBuilder()
		    ->select("mb.recipient")
		    ->from(MailBoxEntry::getClassName(),"mb")
		    ->where("mb.message = :message")
		    ->andWhere("mb.sender != mb.recipient")
		    ->setParameter("message", $id)
		    ->getQuery()->getResult();
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteEntry($id) {
	try {
	    $mbDb = $this->mailboxDao->find($id);
	    if ($mbDb !== null) {
		if ($mbDb->getType() == MailBoxEntryType::DELETED) {
		    $this->invalidateEntityCache($mbDb);
		    $this->mailboxDao->delete($mbDb);
		} else {
		    $this->markAsDeleted($id);
		}
		$this->onDelete($mbDb);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getNewsCount($user) {
	try {
	    return $this->mailboxDao->createQueryBuilder()
		    ->select("COUNT(mb)")->from(MailBoxEntry::getClassName(), "mb")
		    ->where("mb.owner = :owner")
		    ->andWhere("mb.type = :type")
		    ->setParameter("owner", $user->getId())
		    ->setParameter("type", MailBoxEntryType::UNREAD)
		    ->getQuery()->getSingleScalarResult();
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function markAsRead($id) {
	$this->markAs($id, MailBoxEntryType::READ);
    }

    public function markAsUnread($id) {
	$this->markAs($id, MailBoxEntryType::UNREAD);
    }
    
    public function markAsDeleted($id) {
	$this->markAs($id, MailBoxEntryType::DELETED);
    }
    
    private function markAs($id, MailBoxEntryType $mbet) {
	try {
	    $mbDb = $this->mailboxDao->find($id);
	    if ($mbDb !== null) {
		$mbDb->setType($mbet);
		$this->mailboxDao->save($mbDb);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getInboxDatasource($user) {
	$model = new Doctrine(
		$this->mailboxDao->createQueryBuilder("mb")
		    ->where("mb.owner = :owner")
		    ->andWhere("mb.recipient = mb.owner")
		    ->andWhere("mb.sender != mb.recipient")
		    ->setParameter("owner", $this->getMixId($user))
		);
	return $model;
    }

    public function getDeletedDatasource($user) {
	$model = new Doctrine(
		$this->mailboxDao->createQueryBuilder("mb")
		    ->where("mb.owner = :owner")
		    ->andWhere("mb.type = :type")
		    ->setParameter("owner", $this->getMixId($user))
		    ->setParameter("type", MailBoxEntryType::DELETED)
		);
	return $model;
    }

    public function getOutboxDatasource($user) {
	$model = new Doctrine(
		$this->mailboxDao->createQueryBuilder("mb")
		    ->where("mb.owner = :owner")
		    ->andWhere("mb.sender = :owner")
		    ->setParameter("owner", $this->getMixId($user))
		);
	return $model;
    }
}
