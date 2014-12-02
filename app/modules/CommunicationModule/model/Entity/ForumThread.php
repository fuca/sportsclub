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
    \Doctrine\ORM\Mapping\JoinTable,
    \Doctrine\ORM\Mapping\ManyToMany,
    \Doctrine\ORM\Mapping\ManyToOne,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\IIdentifiable,
    \App\SystemModule\Model\Service\ICommentable;

/**
 * ORM persistable entity representing real forum thread
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class ForumThread extends BaseEntity implements IIdentifiable, ICommentable {

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
     * @ORM\Column(type="string", nullable = false)
     */
    protected $title;
    
    /**
     * @ORM\Column(type="string", nullable = false)
     */
    protected $description;
    
    /**
     * @ManyToOne(targetEntity="User", fetch="LAZY", cascade={"merge"})
     * @JoinColumn(nullable = false, name = "author_fk")
     */
    protected $author;
    
    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;
    
    /**
     * @ORM\Column(type="datetime", nullable = false)
     */
    protected $updated;
    
    /**
     * ONE TO MANY UNI
     * @ManyToMany(targetEntity="Comment", cascade={"all"}, fetch="EAGER")
     * @JoinTable(name="Comment_ForumThread",
     *      joinColumns={@JoinColumn(name="thread_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id", unique=true)})
     */
    protected $comments;
    
    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $lastActivity;
    
    /** @ORM\Column(type="CommentMode", nullable = false) */
    protected $commentMode;
    
    /**
     * @ManyToOne(targetEntity="Forum", inversedBy="threads")
     * @JoinColumn(name="forum_id", referencedColumnName="id")
     */
    protected $forum;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->commentMode = CommentMode::SIGNED;
	$this->fromArray($values);
    }
    
    function getId() {
	return $this->id;
    }

    function getAlias() {
	return $this->alias;
    }

    function getTitle() {
	return $this->title;
    }

    function getDescription() {
	return $this->description;
    }

    function getAuthor() {
	return $this->author;
    }

    function getEditor() {
	return $this->editor;
    }

    function getUpdated() {
	return $this->updated;
    }

    function getComments() {
	return $this->comments;
    }

    function getForum() {
	return $this->forum;
    }

    function setId($id) {
	$this->id = $id;
    }

    function setAlias($alias) {
	$this->alias = $alias;
    }

    function setTitle($title) {
	$this->title = $title;
    }

    function setDescription($description) {
	$this->description = $description;
    }

    function setAuthor($author) {
	$this->author = $author;
    }

    function setEditor($editor) {
	$this->editor = $editor;
    }

    function setUpdated($updated) {
	$this->updated = $updated;
    }

    function setComments($comments) {
	$this->comments = $comments;
    }

    function setForum($forum) {
	$this->forum = $forum;
    }
    
    function getCommentMode() {
	return $this->commentMode;
    }

    function setCommentMode($commentMode) {
	$this->commentMode = $commentMode;
    }
    
    function getLastActivity() {
	return $this->lastActivity;
    }

    function setLastActivity($lastActivity) {
	$this->lastActivity = $lastActivity;
    }

    
    public function __toString() {
	return "{$this->getTitle()} (#{$this->getId()})";
    }
}