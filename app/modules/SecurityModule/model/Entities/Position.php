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
    \Doctrine\ORM\Mapping\Id,
    \Doctrine\ORM\Mapping\GeneratedValue,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity representing real position in club
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @licence Apache v2.0
 * 
 * @ORM\Entity
 * @ORM\Table(name="Position", uniqueConstraints={@UniqueConstraint(name="unique_position", columns={"owner_fk", "group_fk", "role_fk"})})
 */
class Position extends BaseEntity {
    
    use EntityMapperTrait;
    
    /**
     * @Id
     * @ORM\Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $name;
    
    /**
     * @ManyToOne(targetEntity="User", inversedBy="positions", cascade={"merge"})
     * @JoinColumn(nullable = false, name = "owner_fk")
     */
    protected $owner;

    /**
     * @ManyToOne(targetEntity="SportGroup")
     * @JoinColumn(nullable = false, name = "group_fk")
     */
    protected $group;

    /**
     * @ManyToOne(targetEntity="Role")
     * @JoinColumn(nullable = false, name = "role_fk")
     */
    protected $role;
    
    /** @ORM\Column(type="boolean", nullable=false) */
    protected $publishContact;
    
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $comment;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->publishContact = false;
	$this->fromArray($values);
    }

    public function getId() {
	return $this->id;
    }

    public function setId($id) {
	$this->id = $id;
    }
        
    public function getOwner() {
	return $this->owner;
    }

    public function getGroup() {
	return $this->group;
    }

    public function getRole() {
	return $this->role;
    }

    public function getComment() {
	return $this->comment;
    }

    public function setOwner($owner) {
	$this->owner = $owner;
    }

    public function setGroup($group) {
	$this->group = $group;
    }

    public function setRole($role) {
	$this->role = $role;
    }

    public function setComment($comment) {
	$this->comment = $comment;
    }
    
    public function getPublishContact() {
	return $this->publishContact;
    }

    public function setPublishContact($publishContact) {
	$this->publishContact = $publishContact;
    }
    
    public function getName() {
	return $this->name;
    }

    public function setName($name) {
	$this->name = $name;
    }
    
    public function __toString() {
	return "{$this->getId()}";
    }

}
