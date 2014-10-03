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
    \App\Model\Misc\Enum\ArticleStatus,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\IIdentifiable;

/**
 * ORM persistable entity representing real article
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class Article extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** @ORM\Column(type="string", nullable = false, unique=true) */
    protected $alias;
    /**
     * @ManyToOne(targetEntity="User", fetch="LAZY", cascade={"merge"})
     * @JoinColumn(nullable = false, name = "author_fk")
     */
    protected $author;

    /** @ORM\Column(type="string", nullable = false) */
    protected $title;

    /** @ORM\Column(type="string", nullable = false) */
    protected $content;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;

    /** @ORM\Column(type="string", nullable = false) */
    protected $pictureName;

    /** @ORM\Column(type="ArticleStatus", nullable = false) */
    protected $status;

    /** @ORM\Column(type="string", nullable = false) */
    protected $highlight;

    /** @ORM\Column(type="integer", nullable = false) */
    protected $counter;

    /** @ORM\Column(type="CommentMode", nullable = false) */
    protected $commentMode;

    /**
     * ONE TO MANY
     * @ManyToMany(targetEntity="Comment", cascade={"remove"})
     * @JoinTable(name="Comment_Article",
     *      joinColumns={@JoinColumn(name="article_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id", unique=true)})
     */
    protected $comments;

    /**
     * @ManyToMany(targetEntity="SportGroup", fetch="EAGER")
     * @JoinTable(name="Article_SportGroup",
      joinColumns={@JoinColumn(name="article_id", referencedColumnName="id")},
      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;

    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="editor_id", referencedColumnName="id")
     */
    protected $editor;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->status = ArticleStatus::DRAFT;
	$this->commentMode = CommentMode::SIGNED;
	$this->highlight = false;
	$this->counter = 0;
	$this->fromArray($values);
    }

    public function getId() {
	return $this->id;
    }

    public function getAlias() {
	return $this->alias;
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

    public function getUpdated() {
	return $this->updated;
    }

    public function getPictureName() {
	return $this->pictureName;
    }

    public function getStatus() {
	return $this->status;
    }

    public function getHighlight() {
	return $this->highlight;
    }

    public function getCounter() {
	return $this->counter;
    }

    public function getCommentMode() {
	return $this->commentMode;
    }

    public function getComments() {
	return $this->comments;
    }

    public function getGroups() {
	return $this->groups;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setAlias($alias) {
	$this->alias = $alias;
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

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setPictureName($pictureName) {
	$this->pictureName = $pictureName;
    }

    public function setStatus($status) {
	$this->status = $status;
    }

    public function setHighlight($highlight) {
	$this->highlight = $highlight;
    }

    public function setCounter($counter) {
	$this->counter = $counter;
    }

    public function setCommentMode($commentMode) {
	$this->commentMode = $commentMode;
    }

    public function setComments($comments) {
	$this->comments = $comments;
    }

    public function setGroups($groups) {
	$this->groups = $groups;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function __toString() {
	return "{$this->getTitle()} (#{$this->getId()})";
    }
}
