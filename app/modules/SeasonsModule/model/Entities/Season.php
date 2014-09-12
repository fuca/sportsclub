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
    Doctrine\ORM\Mapping\OneToMany,
    Doctrine\ORM\Mapping\ManyToOne,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\ArticleStatus,
    \Doctrine\Common\Collections\ArrayCollection,
    \App\Model\IIdentifiable,
    \Nette\DateTime,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity representing season
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */

class Season extends BaseEntity implements IIdentifiable {
    
    use EntityMapperTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /** @ORM\Column(type="string", nullable = false, unique = true) */
    protected $label;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $dateSince;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $dateTill;   
   
    /** @ORM\Column(type="boolean", nullable = false) */
    protected $current;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $updated;
    
    /**
     * @OneToMany(targetEntity="SeasonApplication", mappedBy="season")
     **/
    protected $applications;

    /**
     * @ManyToOne(targetEntity="User", fetch = "LAZY", cascade = {"MERGE"})
     * @JoinColumn(nullable = true, name = "editor_fk")
     */
    protected $editor;

    /** @ORM\Column(type="string", nullable = false) */
    protected $comment;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->updated = new DateTime();
	$this->fromArray($values);
    }

    public function __toString() {
	return "{$this->getLabel()} ({$this->getId()})";
    }
    
    public function getId() {
	return $this->id;
    }

    public function getLabel() {
	return $this->label;
    }

    public function getDateSince() {
	return $this->dateSince;
    }

    public function getDateTill() {
	return $this->dateTill;
    }

    public function getCurrent() {
	return $this->current;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getApplications() {
	return $this->applications;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getComment() {
	return $this->comment;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setLabel($label) {
	$this->label = $label;
    }

    public function setDateSince($dateSince) {
	$this->dateSince = $dateSince;
    }

    public function setDateTill($dateTill) {
	$this->dateTill = $dateTill;
    }

    public function setCurrent($current) {
	$this->current = $current;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

//    public function setApplications($applications) {
//	if (!$applications instanceof ArrayCollection) {
//	    $this->applications = new ArrayCollection($applications);
//	}
//	$this->applications = $applications;
//    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }

    public function setComment($comment) {
	$this->comment = $comment;
    }
}
