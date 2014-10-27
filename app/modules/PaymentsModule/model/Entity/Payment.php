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
    \App\Model\Misc\Enum\PaymentStatus,
    \App\Model\IIdentifiable,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity payment
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class Payment extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;

   /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(nullable = false, name = "owner_fk")
     */
    protected $owner;

    /**
     * @ManyToOne(targetEntity="Season", fetch = "LAZY")
     * @JoinColumn(nullable = false, name = "season_fk")
     */
    protected $season;

    /** @ORM\Column(type="string", nullable = false) */
    protected $subject;

    /** @ORM\Column(type="string", nullable = false) */
    protected $amount;

    /** @ORM\Column(type="string", nullable = false) */
    protected $vs;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $dueDate;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $orderedDate;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "author_fk")
     */
    protected $editor;

    /** @ORM\Column(type="PaymentStatus", nullable = false) */
    protected $status;

    /** @ORM\Column(type="string", nullable = false) */
    protected $protectedNote;

    /** @ORM\Column(type="string", nullable = false) */
    protected $publicNote;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->status = PaymentStatus::NOT_YET;
	$this->fromArray($values);
    }

    public function __toString() {
	return "{$this->getOwner()} {$this->getAmount()},- (#{$this->getId()})";
    }
    
    public function getId() {
	return $this->id;
    }

    public function getOwner() {
	return $this->owner;
    }

    public function getSeason() {
	return $this->season;
    }

    public function getSubject() {
	return $this->subject;
    }

    public function getAmount() {
	return $this->amount;
    }

    public function getVs() {
	return $this->vs;
    }

    public function getDueDate() {
	return $this->dueDate;
    }

    public function getOrderedDate() {
	return $this->orderedDate;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getStatus() {
	return $this->status;
    }

    public function getProtectedNote() {
	return $this->protectedNote;
    }

    public function getPublicNote() {
	return $this->publicNote;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setOwner($owner) {
	$this->owner = $owner;
    }

    public function setSeason($season) {
	$this->season = $season;
    }

    public function setSubject($subject) {
	$this->subject = $subject;
    }

    public function setAmount($amount) {
	$this->amount = $amount;
    }

    public function setVs($vs) {
	$this->vs = $vs;
    }

    public function setDueDate($dueDate) {
	$this->dueDate = $dueDate;
    }

    public function setOrderedDate($orderedDate) {
	$this->orderedDate = $orderedDate;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setStatus($status) {
	$this->status = $status;
    }

    public function setProtectedNote($protectedNote) {
	$this->protectedNote = $protectedNote;
    }

    public function setPublicNote($publicNote) {
	$this->publicNote = $publicNote;
    }
}
