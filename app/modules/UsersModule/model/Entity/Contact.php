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
    Doctrine\ORM\Mapping\OneToOne,
    Doctrine\ORM\Mapping\JoinColumn,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \Nette\DateTime,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\IIdentifiable;

/**
 * ORM persistable entity representing real users contact
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class Contact extends BaseEntity implements IIdentifiable {

    use EntityMapperTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @OneToOne(targetEntity = "Address", cascade={"all"}, orphanRemoval=true, fetch="EAGER")
     * @JoinColumn(name = "address_fk", referencedColumnName = "id", nullable=true)
     */
    protected $address;

    /** @ORM\Column(type="string", nullable=true) */
    protected $phone;

    /** @ORM\Column(type="string", nullable=false, unique=true) */
    protected $email;

    /** @ORM\Column(type="string", nullable=true) */
    protected $job;

    /** @ORM\Column(type="string", nullable=true) */
    protected $contPersonName;

    /** @ORM\Column(type="string", nullable=true) */
    protected $contPersonPhone;

    /** @ORM\Column(type="string", nullable=true) */
    protected $contPersonMail;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected $updated;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->fromArray($values);
    }

    public function getId() {
	return $this->id;
    }

    public function getAddress() {
	return $this->address;
    }

    public function getPhone() {
	return $this->phone;
    }

    public function getEmail() {
	return $this->email;
    }

    public function getJob() {
	return $this->job;
    }

    public function getContPersonName() {
	return $this->contPersonName;
    }

    public function getContPersonPhone() {
	return $this->contPersonPhone;
    }

    public function getContPersonMail() {
	return $this->contPersonMail;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function setAddress($address) {
	$this->address = $address;
    }

    public function setPhone($phone) {
	$this->phone = $phone;
    }

    public function setEmail($email) {
	$this->email = $email;
    }

    public function setJob($job) {
	$this->job = $job;
    }

    public function setContPersonName($contPersonName) {
	$this->contPersonName = $contPersonName;
    }

    public function setContPersonPhone($contPersonPhone) {
	$this->contPersonPhone = $contPersonPhone;
    }

    public function setContPersonMail($contPersonMail) {
	$this->contPersonMail = $contPersonMail;
    }

    public function setUpdated(DateTime $updated) {
	$this->updated = $updated;
    }

    public function setId($id) {
	$this->id = $id;
    }
    
    public function __toString() {
	return "{$this->getId()}";
    }

}
