<?php

/*
 * Copyright 2014 fuca.
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
    Doctrine\ORM\Mapping\JoinColumn,
    Doctrine\ORM\Mapping\ManyToOne,
    Doctrine\ORM\Mapping\Id,
    Doctrine\ORM\Mapping\UniqueConstraint,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\IIdentifiable,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity season tax
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="SeasonTax",
 * uniqueConstraints={@UniqueConstraint(name="unique_season_tax", columns={"season_fk", "group_fk"})})
 */
class SeasonTax extends BaseEntity implements IIdentifiable {

    use EntityMapperTrait;

    /**
     * @Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Season", fetch = "EAGER")
     * @JoinColumn(nullable = false, name = "season_fk")
     */
    protected $season;

    /**
     * @ManyToOne(targetEntity="SportGroup", fetch = "EAGER")
     * @JoinColumn(nullable = false, name = "group_fk")
     */
    protected $sportGroup;

    /** @ORM\Column(type="integer", nullable = true) */
    protected $credit;

    /** @ORM\Column(type="integer", nullable = true) */
    protected $memberShip;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $changed;

    /** @ORM\Column(type="string", nullable = false) */
    protected $comment;

    public function __construct(array $data) {
	parent::__construct();
	$this->fromArray($data);
    }

    public function getId() {
	return $this->id;
    }

    public function getSeason() {
	return $this->season;
    }

    public function getSportGroup() {
	return $this->sportGroup;
    }

    public function getCredit() {
	return $this->credit;
    }

    public function getMemberShip() {
	return $this->memberShip;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getChanged() {
	return $this->changed;
    }

    public function getComment() {
	return $this->comment;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setSeason($season) {
	$this->season = $season;
    }

    public function setSportGroup($sportGroup) {
	$this->sportGroup = $sportGroup;
    }

    public function setCredit($credit) {
	$this->credit = $credit;
    }

    public function setMemberShip($memberShip) {
	$this->memberShip = $memberShip;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setChanged($changed) {
	$this->changed = $changed;
    }

    public function setComment($comment) {
	$this->comment = $comment;
    }

    public function __toString() {
	return "{$this->getSeason()} {$this->getSportGroup()}";
    }

}
