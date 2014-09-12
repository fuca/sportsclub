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
    Doctrine\ORM\Mapping\JoinColumn,
    Doctrine\ORM\Mapping\JoinTable,
    Doctrine\ORM\Mapping\ManyToMany,
    Doctrine\ORM\Mapping\ManyToOne,
    Doctrine\ORM\Mapping\OneToMany,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\ArticleStatus,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\IIdentifiable;

/**
 * ORM persistable entity representing post at group's wall
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class WallPost extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

//    /**
//     * @ManyToOne(fetch = FetchType.LAZY, cascade = {CascadeType.REFRESH, CascadeType.MERGE}) 
//     * @JoinColumn(nullable = true, name = "author_fk")
//     */
//    
//    private User author;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;

    /**
     * @ManyToMany(targetEntity="SportGroup", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinTable(name="WallPost_SportGroup",
      joinColumns={@JoinColumn(name="wallpost_id", referencedColumnName="id")},
      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;

    /** @ORM\Column(type="string", nullable = false) */
    protected $title;

    /** @ORM\Column(type="string", nullable = false) */
    protected $content;

    /** @ORM\Column(type="ArticleStatus", nullable = false) */
    protected $status;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $showFrom;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $showTo;

    /** @ORM\Column(type="CommentMode", nullable = false) */
    protected $commentMode;

    /**
     * ONE TO MANY
     * @ManyToMany(targetEntity="Comment", cascade={"remove"})
     * @JoinTable(name="Comment_WallPost",
     *      joinColumns={@JoinColumn(name="wallpost_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id", unique=true)})
     */
    protected $comments;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"merge"})
     * @JoinColumn(nullable = false, name = "editor_fk")
     */
    protected $editor;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->status = ArticleStatus::DRAFT;
	$this->commentMode = CommentMode::ALLOWED;
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getGroups() {
	return $this->groups;
    }

    public function getTitle() {
	return $this->title;
    }

    public function getContent() {
	return $this->content;
    }

    public function getStatus() {
	return $this->status;
    }

    public function getShowFrom() {
	return $this->showFrom;
    }

    public function getShowTo() {
	return $this->showTo;
    }

    public function getCommentMode() {
	return $this->commentMode;
    }

    public function getComments() {
	return $this->comments;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setGroups($groups) {
	$this->groups = $groups;
    }

    public function setTitle($title) {
	$this->title = $title;
    }

    public function setContent($content) {
	$this->content = $content;
    }

    public function setStatus($status) {
	$this->status = $status;
    }

    public function setShowFrom($showFrom) {
	$this->showFrom = $showFrom;
    }

    public function setShowTo($showTo) {
	$this->showTo = $showTo;
    }

    public function setCommentMode($commentMode) {
	$this->commentMode = $commentMode;
    }

    public function setComments($comments) {
	$this->comments = $comments;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function __toString() {
	return "{$this->getTitle()} (#{$this->getId()})";
    }

}
