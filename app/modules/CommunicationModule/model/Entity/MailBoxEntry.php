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

namespace App\Model\Entities;

use \Doctrine\ORM\Mapping as ORM,
    \Doctrine\ORM\Mapping\JoinColumn,
    \Doctrine\ORM\Mapping\ManyToOne,
    \Doctrine\ORM\Mapping\UniqueConstraint,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\Misc\Enum\MailBoxEntryType;

/**
 * Description of MailBox
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="Mailbox", uniqueConstraints={@UniqueConstraint(name="unique_mailboxentry", columns={"owner_fk", "message_fk"})})
 */
class MailBoxEntry extends BaseEntity {
    
    use EntityMapperTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * @ORM\Column(type="MailBoxEntryType", nullable = false)
     */
    protected $type;
    
    /**
     * @ORM\Column(type="boolean", nullable = false)
     */
    protected $starred;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="owner_fk", referencedColumnName="id", nullable = false)
     */
    protected $owner;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(name="sender_fk", referencedColumnName="id")
     */
    protected $sender;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(name="recipient_fk", referencedColumnName="id")
     */
    protected $recipient;
    
    /**
     * @ManyToOne(targetEntity="PrivateMessage", fetch = "EAGER", cascade={"PERSIST"})
     * @JoinColumn(name="message_fk", referencedColumnName="id", nullable = false)
     **/
    protected $message;
    
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->type = MailBoxEntryType::UNREAD;
	$this->starred = false;
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getType() {
	return $this->type;
    }

    public function getStarred() {
	return $this->starred;
    }

    public function getOwner() {
	return $this->owner;
    }

    public function getMessage() {
	return $this->message;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setType($type) {
	$this->type = $type;
    }

    public function setStarred($starred) {
	$this->starred = $starred;
    }

    public function setOwner($owner) {
	$this->owner = $owner;
    }

    public function setMessage($message) {
	$this->message = $message;
    }
    
    public function getSender() {
	return $this->sender;
    }

    public function getRecipient() {
	return $this->recipient;
    }

    public function setSender($sender) {
	$this->sender = $sender;
    }

    public function setRecipient($recipient) {
	$this->recipient = $recipient;
    }
    
}
