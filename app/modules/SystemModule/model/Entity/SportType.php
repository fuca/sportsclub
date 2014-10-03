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
    \Kdyby\Doctrine\Entities\BaseEntity,
    Doctrine\ORM\Mapping\JoinColumn,
    Doctrine\ORM\Mapping\Id,
    Doctrine\ORM\Mapping\GeneratedValue,
    \App\Model\IIdentifiable,
    \App\Model\Misc\EntityMapperTrait;
/**
 * ORM persistable entity representing sport type
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class SportType extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;
    
    /**
     * @Id
     * @ORM\Column(type="integer")
     * @GeneratedValue
     * @var int
     */
    protected $id;
    
    /** @ORM\Column(type="string", nullable=false) */
    protected $name; 
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $image;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $note;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getName() {
	return $this->name;
    }

    public function getImage() {
	return $this->image;
    }

    public function getNote() {
	return $this->note;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setName($name) {
	$this->name = $name;
    }

    public function setImage($imgName) {
	$this->image = $imgName;
    }

    public function setNote($note) {
	$this->note = $note;
    }
    
    public function __toString() {
	return "{$this->getName()} ({$this->getId()})";
    }
}
