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
    \Nette\Utils\DateTime,
    \App\Model\Misc\EntityMapperTrait;

/**
 * Partner entity
 * @ORM\Entity
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class Partner extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;

   /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** @ORM\Column(type="string", nullable = false, unique = true) */
    protected $name;
    
    /** @ORM\Column(type="text", nullable = false) */
    protected $link;
    
    /** @ORM\Column(type="boolean", nullable = false) */
    protected $active;
    
    /** @ORM\Column(type="string", nullable = false) */
    protected $picture;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = false, name = "referrer_fk")
     */
    protected $referrer;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;
    
    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;
    
    /** @ORM\Column(type="text", nullable = false) */
    protected $note;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->updated = new DateTime();
	$this->note = "";
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getName() {
	return $this->name;
    }

    public function getLink() {
	return $this->link;
    }

    public function getActive() {
	return $this->active;
    }

    public function getPicture() {
	return $this->picture;
    }

    public function getReferrer() {
	return $this->referrer;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getUpdated() {
	return $this->updated;
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

    public function setLink($link) {
	$this->link = $link;
    }

    public function setActive($active) {
	$this->active = $active;
    }

    public function setPicture($picture) {
	$this->picture = $picture;
    }

    public function setReferrer($referrer) {
	$this->referrer = $referrer;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setNote($note) {
	$this->note = $note;
    }

    public function __toString() {
	return "{$this->getName()}";
    }
}
