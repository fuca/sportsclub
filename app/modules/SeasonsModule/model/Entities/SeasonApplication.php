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
    \Doctrine\ORM\Mapping\OneToOne,
    \Doctrine\ORM\Mapping\ManyToOne,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\EntityMapperTrait,
    \Doctrine\ORM\Mapping\UniqueConstraint,
    \Nette\DateTime;

/**
 * ORM persistable entity season application
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="SeasonApplication",
 * uniqueConstraints={@UniqueConstraint(name="unique_season_app", columns={"season_fk", "group_fk", "owner_fk"})})
 */

class SeasonApplication extends BaseEntity {
    
    use EntityMapperTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * @ManyToOne(targetEntity="Season", inversedBy="applications")
     * @JoinColumn(name="season_fk", referencedColumnName="id")
     */
    protected $season;
    
    /** @ORM\Column(type="datetime", nullable = false) */
    protected $enrolledTime;
    
    /**
     * @OneToOne(targetEntity="Payment", fetch = "EAGER", cascade={"REMOVE"})
     * @JoinColumn(name = "payment_fk", nullable = true)
     */
    protected $payment; 
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(name = "owner_fk")
     */
    protected $owner;
    
    /**
     * @ManyToOne(targetEntity="SportGroup", fetch = "LAZY")
     * @JoinColumn(name = "group_fk")
     */
    protected $sportGroup;
    
    /** @ORM\Column(type="string", nullable = true) */
    protected $documentPath;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "EAGER", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;
    
    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;
    
    /** @ORM\Column(type="string", nullable = true) */
    protected $comment;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->updated = new DateTime();
	$this->fromArray($values);
    }

    public function __toString() {
	return "{$this->getOwner()} {$this->getSeason()} {$this->getSportGroup()}";
    }
    
    public function getId() {
	return $this->id;
    }

    public function getSeason() {
	return $this->season;
    }

    public function getEnrolledTime() {
	return $this->enrolledTime;
    }

   

    public function getPayment() {
	return $this->payment;
    }

    public function getOwner() {
	return $this->owner;
    }


    public function getDocumentPath() {
	return $this->documentPath;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getUpdated() {
	return $this->updated;
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

    public function setEnrolledTime($enrolledTime) {
	$this->enrolledTime = $enrolledTime;
    }

    public function setPayment($payment) {
	$this->payment = $payment;
    }

    public function setOwner($owner) {
	$this->owner = $owner;
    }

    public function setDocumentPath($documentPath) {
	$this->documentPath = $documentPath;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setUpdated($changed) {
	$this->updated = $changed;
    }

    public function setComment($comment) {
	$this->comment = $comment;
    }
    
    public function getSportGroup() {
	return $this->sportGroup;
    }

    public function setSportGroup($sportGroup) {
	$this->sportGroup = $sportGroup;
    }
    
}
