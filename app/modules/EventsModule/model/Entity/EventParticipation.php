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
    \Kdyby\Doctrine\Entities\BaseEntity, 
    \App\Model\IIdentifiable, 
    \Nette\Utils\DateTime,
    \App\Model\Misc\EntityMapperTrait;
/**
 * Description of EventParticipation relation between User and Event
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class EventParticipation extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="owner_fk", referencedColumnName="id", nullable = false)
     */
    protected $owner;
    
    /**
     * @ManyToOne(targetEntity="Event", inversedBy="participations")
     * @JoinColumn(name="event_fk", referencedColumnName="id", nullable = false)
     */
    protected $event;
    
    /**
     * @ORM\Column(type="EventParticipationType", nullable = false)
     */
    protected $type;
    
    /** 
     * @ORM\Column(type="datetime", nullable = false) 
     */
    protected $updated;
    
    /** 
     * @ORM\Column(type="string", nullable = false) 
     */
    protected $content;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->updated = new DateTime();
	$this->fromArray($values);
    }
    
    public function getOwner() {
	return $this->owner;
    }

    public function getEvent() {
	return $this->event;
    }

    public function getType() {
	return $this->type;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getContent() {
	return $this->content;
    }

    public function setOwner($owner) {
	$this->owner = $owner;
    }

    public function setEvent($event) {
	$this->event = $event;
    }

    public function setType($type) {
	$this->type = $type;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setContent($content) {
	$this->content = $content;
    }
    
    public function getId() {
	return $this->id;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function __toString() {
	return "{$this->getUser()} {$this->getType()} {$this->getEvent()->getId()} {$this->getMessage()}";
    }
}
