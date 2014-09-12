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
    Doctrine\ORM\Mapping\OneToMany,
    Doctrine\ORM\Mapping\ManyToOne,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\ArticleStatus,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\IIdentifiable;

/**
 * ORM persistable entity representing real address
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class StaticPage extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;
      /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "author_fk")
     */
    protected $author;

    /** @ORM\Column(type="string", nullable = false) */
    protected $title;

    /** @ORM\Column(type="string", nullable = false) */
    protected $content;

    /** @ORM\Column(type="string", nullable = false) */
    protected $abbr;

    /** @ORM\Column(type="datetime", nullable = false) */
    
    protected $updated;

    /** @ORM\Column(type="ArticleStatus", nullable = false) */
    protected $status;
    
    /**
     * @OneToMany(targetEntity="StaticPage", mappedBy="parent")
     */
    protected $children;
    
    /**
     * @ManyToOne(targetEntity="StaticPage", inversedBy="children")
     * @JoinColumn(name="parent_id", referencedColumnName="id")
     */
    protected $parent;

    /** @ORM\Column(type="integer", nullable = false) */
    protected $counter;

    /** @ORM\Column(type="CommentMode", nullable = false) */
    protected $commentMode;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;

    /**
     * @ManyToMany(targetEntity="SportGroup", fetch = "LAZY")
     * @JoinTable(name = "StaticPage_SportGroup",
	    joinColumns = {
		@JoinColumn(name = "page_id", referencedColumnName = "id")},
	    inverseJoinColumns = {
		@JoinColumn(name = "group_id", referencedColumnName = "id")})
     */
    protected $groups;

    /**
     * ONE TO MANY
     * @ManyToMany(targetEntity="Comment", cascade={"remove"})
     * @JoinTable(name="Comment_StaticPage",
     *      joinColumns={@JoinColumn(name="page_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id", unique=true)})
     */
    protected $comments;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->status = ArticleStatus::DRAFT;
	$this->commentMode = CommentMode::RESTRICTED;
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

    public function getContent() {
	return $this->content;
    }

    public function getAbbr() {
	return $this->abbr;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getStatus() {
	return $this->status;
    }

    public function getChildren() {
	return $this->children;
    }

    public function getParent() {
	return $this->parent;
    }

    public function getCounter() {
	return $this->counter;
    }

    public function getCommentMode() {
	return $this->commentMode;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getGroups() {
	return $this->groups;
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

    public function setContent($content) {
	$this->content = $content;
    }

    public function setAbbr($abbr) {
	$this->abbr = $abbr;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setStatus($status) {
	$this->status = $status;
    }

    public function setChildren($children) {
	$this->children = $children;
    }

    public function setParent($parent) {
	$this->parent = $parent;
    }

    public function setCounter($counter) {
	$this->counter = $counter;
    }

    public function setCommentMode($commentMode) {
	$this->commentMode = $commentMode;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setGroups($groups) {
	$this->groups = $groups;
    }

    public function setComments($comments) {
	$this->comments = $comments;
    }

    public function __toString() {
	return "{$this->getTitle()} (#{$this->getId()}))";
    }
}
