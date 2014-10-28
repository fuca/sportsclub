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
    \Doctrine\ORM\Mapping\ManyToMany,
    \Doctrine\ORM\Mapping\JoinTable,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\EventVisibility,
    \App\Model\Misc\Enum\EventType,
    \App\Model\Misc\EntityMapperTrait,
    \Doctrine\Common\Collections\ArrayCollection,
    \Nette\DateTime,
    \App\Model\IIdentifiable,
    \App\SystemModule\Model\Service\ICommentable;

/**
 * ORM persistable entity representing real event
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class Event extends BaseEntity Implements IIdentifiable, ICommentable {
    
    use EntityMapperTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** @ORM\Column(type="string", nullable = false, unique=true) */
    protected $alias;

    /** @ORM\Column(type="EventType", nullable = false) */
    protected $eventType;

    /** @ORM\Column(type="string", nullable = false) */
    protected $title;

    /** @ORM\Column(type="string", nullable = false) */
    protected $description;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $takePlaceSince;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $takePlaceTill;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $confirmUntil;

    /** @ORM\Column(type="EventVisibility", nullable = false) */
    protected $visibility;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = false, name = "author_fk")
     */
    protected $author;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;

    /** @ORM\Column(type="CommentMode", nullable = false) */
    protected $commentMode;

    /**
     * @ManyToMany(targetEntity="SportGroup", fetch="EAGER")
     * @JoinTable(name="Event_SportGroup",
      joinColumns={@JoinColumn(name="event_id", referencedColumnName="id")},
      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;

    /**
     * ONE TO MANY
     * @ManyToMany(targetEntity="Comment", cascade={"all"}, fetch="EAGER")
     * @JoinTable(name="Comment_Event",
     *      joinColumns={@JoinColumn(name="event_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id", unique=true)})
     */
    protected $comments;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->eventType = EventType::TRAINING;
	$this->visibility = EventVisibility::GROUP;
	$this->commentMode = CommentMode::ALLOWED;
	$this->updated = new DateTime();
	//$this->groups = new ArrayCollection();
	$this->fromArray($values);
    }
    public function getId() {
	return $this->id;
    }

    public function getAlias() {
	return $this->alias;
    }

    public function getEventType() {
	return $this->eventType;
    }

    public function getTitle() {
	return $this->title;
    }

    public function getDescription() {
	return $this->description;
    }

    public function getTakePlaceSince() {
	return $this->takePlaceSince;
    }

    public function getTakePlaceTill() {
	return $this->takePlaceTill;
    }

    public function getConfirmUntil() {
	return $this->confirmUntil;
    }

    public function getVisibility() {
	return $this->visibility;
    }

    public function getAuthor() {
	return $this->author;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getCommentMode() {
	return $this->commentMode;
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

    public function setAlias($alias) {
	$this->alias = $alias;
    }

    public function setEventType($eventType) {
	$this->eventType = $eventType;
    }

    public function setTitle($title) {
	$this->title = $title;
    }

    public function setDescription($description) {
	$this->description = $description;
    }

    public function setTakePlaceSince($takePlaceSince) {
	$this->takePlaceSince = $takePlaceSince;
    }

    public function setTakePlaceTill($takePlaceTill) {
	$this->takePlaceTill = $takePlaceTill;
    }

    public function setConfirmUntil($confirmUntil) {
	$this->confirmUntil = $confirmUntil;
    }

    public function setVisibility($visibility) {
	$this->visibility = $visibility;
    }

    public function setAuthor($author) {
	$this->author = $author;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setCommentMode($commentMode) {
	$this->commentMode = $commentMode;
    }

    public function setGroups($groups) {
	$this->groups = $groups;
    }

    public function setComments($comments) {
	$this->comments = $comments;
    }
    
    public function __toString() {
	return "{$this->getTitle()} (#{$this->getId()})";
    }

}
