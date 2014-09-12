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
    \App\Model\Misc\EntityMapperTrait;

/**
 * Persistable entity representing real collection of media entities called Gallery
 * @author <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class Gallery extends BaseEntity {
    
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
    
    /**
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="editor_id", referencedColumnName="id")
     */
    protected $editor;
    
    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;
    
    /** @ORM\Column(type="string", nullable = false) */
    protected $imgName;
    
    /**
     * @ManyToMany(targetEntity="SportGroup", fetch="LAZY")
     * @JoinTable(name="Gallery_SportGroup",
      joinColumns={@JoinColumn(name="gallery_id", referencedColumnName="id")},
      inverseJoinColumns={@JoinColumn(name="group_id", referencedColumnName="id")})
     */
    protected $groups;
    
        /**
     * @ORM\Column(type="string", nullable = false)
     */
    protected $description ;
    
   /**
     * ONE TO MANY
     * @ManyToMany(targetEntity="Media", cascade={"remove"})
     * @JoinTable(name="Media_Gallery",
     *      joinColumns={@JoinColumn(name="gallery_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="media_id", referencedColumnName="id", unique=true)})
     */
    protected $comments;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
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

    public function getImgName() {
	return $this->imgName;
    }

    public function getGroups() {
	return $this->groups;
    }

    public function getDescription() {
	return $this->description;
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

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setImgName($imgName) {
	$this->imgName = $imgName;
    }

    public function setGroups($groups) {
	$this->groups = $groups;
    }

    public function setDescription($description) {
	$this->description = $description;
    }

    public function setComments($comments) {
	$this->comments = $comments;
    }

    public function __toString() {
	return "{$this->getId()}";
    }
    
}
