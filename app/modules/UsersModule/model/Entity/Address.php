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
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity representing real address
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="Address")
 */
class Address extends BaseEntity {
    
    use EntityMapperTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** @ORM\Column(nullable = false) */
    protected $city;

    /** @ORM\Column(nullable = false) */
    protected $postalCode;

    /** @ORM\Column(nullable = false) */
    protected $street;

    /** @ORM\Column(nullable = false) */
    protected $number;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $accountNumber;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->fromArray($values);
    }

    public function getId() {
	return $this->id;
    }

    public function getCity() {
	return $this->city;
    }

    public function getPostalCode() {
	return $this->postalCode;
    }

    public function getStreet() {
	return $this->street;
    }

    public function getNumber() {
	return $this->number;
    }

    public function setCity($city) {
	$this->city = $city;
    }

    public function setPostalCode($postalCode) {
	$this->postalCode = $postalCode;
    }

    public function setStreet($street) {
	$this->street = $street;
    }

    public function setNumber($number) {
	$this->number = $number;
    }

//    public function setId($id) {
//	$this->id = $id;
//    }

    
    
}
