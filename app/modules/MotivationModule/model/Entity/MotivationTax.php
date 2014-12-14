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
    \App\Model\IIdentifiable,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity motivation tax
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="MotivationTax",
 * uniqueConstraints={@UniqueConstraint(name="unique_motivation_tax", columns={"season_fk", "group_fk"})})
 */
class MotivationTax extends BaseEntity implements IIdentifiable {
    
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
     * @ManyToOne(targetEntity="SportGroup", fetch = "EAGER")
     * @JoinColumn(nullable = false, name = "group_fk")
     */
    protected $sportGroup;

    /** @ORM\Column(type="integer", nullable = false) */
    protected $credit;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $orderedDate;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "author_fk")
     */
    protected $editor;

    /** @ORM\Column(type="string", nullable = false) */
    protected $publicNote;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->fromArray($values);
    }

    public function __toString() {
	return "{$this->getSportGroup()} {$this->getSeason()} (#{$this->getId()})";
    }
    
    public function getCredit() {
	return $this->credit;
    }

    public function setCredit($credit) {
	$this->credit = $credit;
    }

    public function getId() {
	return $this->id;
    }

    public function getSeason() {
	return $this->season;
    }

    public function getOrderedDate() {
	return $this->orderedDate;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getPublicNote() {
	return $this->publicNote;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setSeason($season) {
	$this->season = $season;
    }

    public function setOrderedDate($orderedDate) {
	if (!empty($orderedDate))
	    $this->orderedDate = $orderedDate;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setPublicNote($publicNote) {
	$this->publicNote = $publicNote;
    }
    
    public function getSportGroup() {
	return $this->sportGroup;
    }

    public function setSportGroup($sportGroup) {
	$this->sportGroup = $sportGroup;
    }
}
