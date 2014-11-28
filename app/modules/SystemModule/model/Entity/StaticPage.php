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
    \Doctrine\ORM\Mapping\OneToMany,
    \Doctrine\ORM\Mapping\ManyToOne,
    \Doctrine\ORM\Mapping\UniqueConstraint,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Entities\SportGroup,
    \App\Model\Misc\Enum\StaticPageStatus,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\IIdentifiable,
    \App\SystemModule\Model\Service\ICommentable;

/**
 * ORM persistable entity representing real address
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
// @ORM\Table(name="StaticPage", uniqueConstraints={@UniqueConstraint(name="unique_static_page", columns={"group_fk", "abbr"})})
class StaticPage extends BaseEntity implements IIdentifiable, ICommentable {
    
    use EntityMapperTrait;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /** @ORM\Column(type="string", nullable = false) */
    protected $title;

    /** @ORM\Column(type="text", nullable = false) */
    protected $content;

    /** @ORM\Column(type="string", nullable = false, unique = true) */
    protected $abbr;

    /** @ORM\Column(type="datetime", nullable = false) */
    
    protected $updated;

    /** @ORM\Column(type="ArticleStatus", nullable = false) */
    protected $status;
    
//    /**
//     * @OneToMany(targetEntity="StaticPage", mappedBy="parent")
//     */
//    protected $children;
//    
//    /**
//     * @ManyToOne(targetEntity="StaticPage", inversedBy="children")
//     * @JoinColumn(name="parent_id", referencedColumnName="id", nullable = true)
//     */
//    protected $parent;

//    /** @ORM\Column(type="integer", nullable = false) */
//    protected $counter;

    /** @ORM\Column(type="CommentMode", nullable = false) */
    protected $commentMode;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;
    
    /**
     * @ManyToOne(targetEntity="SportGroup")
     * @JoinColumn(name="group_fk", referencedColumnName="id", nullable = true)
     **/
    protected $group;

    /**
     * ONE TO MANY UNI
     * @ManyToMany(targetEntity="Comment", cascade={"remove"})
     * @JoinTable(name="Comment_StaticPage",
     *      joinColumns={@JoinColumn(name="page_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id", unique = true)})
     */
    protected $comments;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->status = StaticPageStatus::DRAFT;
	$this->commentMode = CommentMode::RESTRICTED;
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

    public function getAbbr() {
	return $this->abbr;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getStatus() {
	return $this->status;
    }

//    public function getChildren() {
//	return $this->children;
//    }

//    public function getParent() {
//	return $this->parent;
//    }

    public function getCommentMode() {
	return $this->commentMode;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getGroup() {
	return $this->group;
    }

    public function getComments() {
	return $this->comments;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setTitle($title) {
	if (!empty($title))
	    $this->title = $title;
    }

    public function setContent($content) {
	$this->content = $content;
    }

    public function setAbbr($abbr) {
	if (!empty($abbr))
	    $this->abbr = $abbr;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setStatus($status) {
	$this->status = $status;
    }

//    public function setChildren($children) {
//	if (!empty($children))
//	    $this->children = $children;
//    }
//
//    public function setParent($parent) {
//	$this->parent = $parent;
//    }

    public function setCommentMode($commentMode) {
	$this->commentMode = $commentMode;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setGroup($group) {
	$this->group = $group;
	if ($group instanceof SportGroup)
	    if (!$group->getStaticPages()->contains($this))
		$group->addStaticPage($this);
    }

    public function setComments($comments) {
	$this->comments = $comments;
    }

    public function __toString() {
	return "{$this->getTitle()} (#{$this->getId()}))";
    }
}
