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
    \Doctrine\ORM\Mapping\MappedSuperclass,
    \Doctrine\ORM\Mapping\ManyToOne,
    \Doctrine\ORM\Mapping\JoinColumn,
    \Doctrine\ORM\Mapping\InheritanceType,
    \Doctrine\ORM\Mapping\DiscriminatorColumn,
    \App\Model\IIdentifiable,
    \App\Model\Misc\EntityMapperTrait;

/**
 * Abstract entity for application comments
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @ORM\Entity
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
abstract class Comment extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** @ORM\Column(type="string", nullable=false) */
    protected $title;
    
    /** @ORM\Column(type="text", nullable=false) */
    protected $content;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected $created;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected $updated;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY")
     * @JoinColumn(nullable = true, name = "author_fk")
     */
    protected $author;
    
    /**
     * @ManyToOne(targetEntity="User", cascade = {"MERGE"})
     * @JoinColumn(name="editor_fk", referencedColumnName="id")
     */
    protected $editor;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getTitle() {
	return $this->title;
    }

    public function getContent() {
	return $this->content;
    }

    public function getCreated() {
	return $this->created;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getAuthor() {
	return $this->author;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setTitle($title) {
	$this->title = $title;
    }

    public function setContent($content) {
	$this->content = $content;
    }

    public function setCreated($created) {
	$this->created = $created;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setAuthor($author) {
	$this->author = $author;
    }
    
    public function getEditor() {
	return $this->editor;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    
    public function __toString() {
	return "{$this->getTitle()} (#{$this->getId()})";
    }
        
    /* Jsme ve stavu, kdy je jedna tabulka Comment a ostatni se na ni napojuji
     * Kdyz to budu chtit predelat zpet na stav, kdy ma kazdy typ commentu svou tabulku, 
     * tak pridam anotaci @MappedSuperClass a smazu tuhle @inheritanci a @discriminatorColumn 
     * a do kazde commentovane entity pripisu do targetEntity realny typ commentu, ktery se bude mapovat
     */
    
    /* Jsme ve stavu, kdy kazda commentovatelna entita ma svou tabulku commentu
     * Kdyz chci do stavu spolecne tabulky, tak musim zde do anotaci pridat @InheritanceType("SINGLE_TABLE")
     * a @DiscriminatorColumn(name="type", type="string") a smazat @MappedSuperClass.
     * A do kazde commentovatelne entity do targetEntity dam typ Comment
     */
    
}
