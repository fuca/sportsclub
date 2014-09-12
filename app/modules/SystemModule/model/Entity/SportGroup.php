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
    Doctrine\ORM\Mapping\OneToMany,
    Doctrine\ORM\Mapping\ManyToOne,
    Doctrine\ORM\Mapping\JoinColumn,    
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\IIdentifiable,
    \App\Model\Misc\EntityMapperTrait;


/**
 * ORM persistable entity representing real sport group
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */

class SportGroup extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;
    
   /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;
    
    /** @ORM\Column(type="string", nullable=false) */
    protected $name;
    
    /** @ORM\Column(type="string", nullable=false) */
    protected $description;
    
    /** @ORM\Column(type="string", nullable=false, unique=true) */
    protected $abbr;
    
    /**
     * @OneToMany(targetEntity="SportGroup", mappedBy="parent")
     */
    protected $children;
    
    /** @ORM\Column(type="datetime", nullable = false) */
    protected $appDate;
    
    /**
     * @ManyToOne(targetEntity="SportGroup", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;
    
    /** @ORM\Column(type="smallint", nullable=false) */
    protected $priority;
    
    /** @ORM\Column(type="boolean", nullable=false) */
    protected $activity;
    
    /**
     * @ManyToOne(targetEntity="SportType")
     * @JoinColumn(name="sportType_id", referencedColumnName="id", nullable=false)
     */
    protected $sportType;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->activity = false;
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getName() {
	return $this->name;
    }

    public function getDescription() {
	return $this->description;
    }

    public function getAbbr() {
	return $this->abbr;
    }

    public function getChildren() {
	return $this->children;
    }

    public function getParent() {
	return $this->parent;
    }

    public function getPriority() {
	return $this->priority;
    }

    public function getActivity() {
	return $this->activity;
    }

    public function getSportType() {
	return $this->sportType;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setName($name) {
	$this->name = $name;
    }

    public function setDescription($description) {
	$this->description = $description;
    }

    public function setAbbr($abbr) {
	$this->abbr = $abbr;
    }

    public function setChildren($children) {
	$this->children = $children;
    }

    public function setParent($parent) {
	$this->parent = $parent;
    }

    public function setPriority($priority) {
	$this->priority = $priority;
    }

    public function setActivity($activity) {
	$this->activity = $activity;
    }

    public function setSportType($sportType) {
	$this->sportType = $sportType;
    }
    public function getAppDate() {
	return $this->appDate;
    }

    public function setAppDate($appDate) {
	$this->appDate = $appDate;
    }

    
    public function __toString() {
	return "{$this->getName()} ({$this->getId()})";
    }
    
}
