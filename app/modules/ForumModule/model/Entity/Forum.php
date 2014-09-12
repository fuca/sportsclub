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
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity representing real forum thread
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class Forum extends BaseEntity {

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
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;

    /**
     * @ORM\Column(type="datetime", nullable = false)
     */
    protected $updated;

    /**
     * @ORM\Column(type="datetime", nullable = false)
     */
    protected $lastActivity;

    /**
     * @ORM\Column(type="string", nullable = false)
     */
    protected $title;

    /**
     * @ManyToMany(targetEntity="SportGroup", fetch="LAZY")
     * @JoinTable(name="Forum_SportGroup",
      joinColumns={
     * @JoinColumn(name="forum_id", referencedColumnName="id")},
      inverseJoinColumns={
     * @JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;

    /**
     * @ORM\Column(type="string", nullable = false)
     */
    protected $description;

    /**
     * @ORM\Column(type="string", nullable = false)
     */
    protected $imgName;

    /**
     * @ORM\Column(type="CommentMode", nullable = false)
     */
    protected $commentMode;

    /**
     * ONE TO MANY
     * @ManyToMany(targetEntity="Comment", cascade={"remove"})
     * @JoinTable(name="Comment_Forum",
     *      joinColumns={@JoinColumn(name="forum_id", referencedColumnName="id")},
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

    public function getAlias() {
	return $this->alias;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getLastActivity() {
	return $this->lastActivity;
    }

    public function getTitle() {
	return $this->title;
    }

    public function getGroups() {
	return $this->groups;
    }

    public function getDescription() {
	return $this->description;
    }

    public function getImgName() {
	return $this->imgName;
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

    public function setAlias($alias) {
	$this->alias = $alias;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setLastActivity($lastActivity) {
	$this->lastActivity = $lastActivity;
    }

    public function setTitle($title) {
	$this->title = $title;
    }

    public function setGroups($groups) {
	$this->groups = $groups;
    }

    public function setDescription($description) {
	$this->description = $description;
    }

    public function setImgName($imgName) {
	$this->imgName = $imgName;
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
