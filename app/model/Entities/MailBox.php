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

use Doctrine\ORM\Mapping as ORM,
    Doctrine\ORM\Mapping\UniqueConstraint,
    Doctrine\ORM\Mapping\JoinColumn,
    Doctrine\ORM\Mapping\ManyToOne,
    Doctrine\ORM\Mapping\Id,
    \Kdyby\Doctrine\Entities\BaseEntity,
    App\Model\Misc\Enum\PrivateMessageStatus,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity mailbox
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="Mailbox", uniqueConstraints={@UniqueConstraint(name="unique_mailboxEntry", columns={"owner_fk", "message_fk"})})
 */
class MailBox extends BaseEntity {
    
    use EntityMapperTrait;
    
    /**
     * @Id @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(name="owner_fk", nullable = false)
     */
    protected $owner;
    
    /**
     * @Id @ManyToOne(targetEntity="PrivateMessage", fetch = "EAGER")
     * @JoinColumn(name="message_fk", nullable = false)
     */
    protected $message;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(name = "sender_fk")
     */
    protected $sender;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(name = "recipient_fk")
     */
    protected $recipient;
    
    /** @ORM\Column(type="boolean", nullable=false) */
    protected $starred = false;
    
    /** @ORM\Column(type="PrivateMessageStatus", nullable=false) */
    protected $status;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->status = PrivateMessageStatus::UNREAD;
	$this->fromArray($values);
    }
    
    public function getOwner() {
	return $this->owner;
    }

    public function getMessage() {
	return $this->message;
    }

    public function getSender() {
	return $this->sender;
    }

    public function getRecipient() {
	return $this->recipient;
    }

    public function getStarred() {
	return $this->starred;
    }

    public function getStatus() {
	return $this->status;
    }

    public function setOwner($owner) {
	$this->owner = $owner;
    }

    public function setMessage($message) {
	$this->message = $message;
    }

    public function setSender($sender) {
	$this->sender = $sender;
    }

    public function setRecipient($recipient) {
	$this->recipient = $recipient;
    }

    public function setStarred($starred) {
	$this->starred = $starred;
    }

    public function setStatus($status) {
	$this->status = $status;
    }

    
    public function __toString() {
	return "{$this->getOwner()} - {$this->getMessage()}";
    }
    
}
