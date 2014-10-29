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
    \App\Model\Misc\Enum\MotivationEntryType,
    \App\Model\IIdentifiable,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity motivation entry
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class MotivationEntry extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;

   /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Season", fetch = "LAZY")
     * @JoinColumn(nullable = false, name = "season_fk")
     */
    protected $season;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(nullable = false, name = "owner_fk")
     */
    protected $owner;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "author_fk")
     */
    protected $editor;
    
    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;
    
    /** @ORM\Column(type="integer", nullable = false) */
    protected $amount;
    
    /** @ORM\Column(type="MotivationEntryType", nullable = false) */
    protected $type;
    
    /** @ORM\Column(type="string", nullable = false) */
    protected $subject;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getSeason() {
	return $this->season;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setSeason($season) {
	$this->season = $season;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }
    
    public function getAmount() {
	return $this->amount;
    }

    public function getType() {
	return $this->type;
    }

    public function setAmount($amount) {
	$this->amount = $amount;
    }

    public function setType($type) {
	$this->type = $type;
    }
    
    public function getUpdated() {
	return $this->updated;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }
    
    public function getOwner() {
	return $this->owner;
    }

    public function setOwner($owner) {
	$this->owner = $owner;
    }
    
    public function getSubject() {
	return $this->subject;
    }

    public function setSubject($subject) {
	$this->subject = $subject;
    }
    
    public function __toString() {
	return "{$this->getType()} {$this->getAmount()} (#{$this->getId()})";
    }
    
}
