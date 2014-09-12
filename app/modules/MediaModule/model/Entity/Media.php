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
    Doctrine\ORM\Mapping\JoinColumn,
    Doctrine\ORM\Mapping\JoinTable,
    Doctrine\ORM\Mapping\ManyToMany,
    Doctrine\ORM\Mapping\ManyToOne,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\IIdentifiable;

/**
 * ORM persistable entity representing real Media
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class Media extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * @ManyToOne(targetEntity="User", fetch="LAZY", cascade={"merge"})
     * @JoinColumn(nullable = false, name = "author_fk")
     */
    protected $author;
    
    /** @ORM\Column(type="string", nullable = false) */
    protected $title;
    
    /** @ORM\Column(type="string", nullable = false) */
    protected $dataType;
    
    /** @ORM\Column(type="string", nullable = false) */
    protected $dataPath;
    
    /** @ORM\Column(type="CommentMode", nullable = false) */
    protected $commentMode;
    
    /**
     * ONE TO MANY
     * @ManyToMany(targetEntity="Comment", cascade={"remove"})
     * @JoinTable(name="Comment_Media",
     *      joinColumns={@JoinColumn(name="media_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id", unique=true)})
     */
    protected $comments;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->commentMode = CommentMode::SIGNED;
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getAuthor() {
	return $this->author;
    }

    public function getTitle() {
	return $this->title;
    }

    public function getDataType() {
	return $this->dataType;
    }

    public function getDataPath() {
	return $this->dataPath;
    }

    public function getCommentMode() {
	return $this->commentMode;
    }

    public function getComments() {
	return $this->comments;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setAuthor($author) {
	$this->author = $author;
    }

    public function setTitle($title) {
	$this->title = $title;
    }

    public function setDataType($dataType) {
	$this->dataType = $dataType;
    }

    public function setDataPath($dataPath) {
	$this->dataPath = $dataPath;
    }

    public function setCommentMode($commentMode) {
	$this->commentMode = $commentMode;
    }

    public function setComments($comments) {
	$this->comments = $comments;
    }

        
    public function __toString() {
	return "{$this->getTitle()} (#{$this->getId()})";
    }
}
