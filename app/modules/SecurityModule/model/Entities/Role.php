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
    \Kdyby\Doctrine\Entities\BaseEntity,
    \Doctrine\Common\Collections\ArrayCollection,
    \Doctrine\ORM\PersistentCollection,
    \App\Model\Misc\EntityMapperTrait,
    \App\Model\Misc\Exceptions,
    \Nette\DateTime,
    \App\Model\IIdentifiable;

/**
 * ORM persistable entity representing role
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */

class Role extends BaseEntity implements IIdentifiable {

    use EntityMapperTrait;
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var int
     */
    protected $id;

    /** @ORM\Column(type="string", nullable = false, unique=true) */
    protected $name;
    
     /**
     * @ManyToMany(targetEntity="Role", mappedBy="parents")
     */
    private $children;

    /**
     * @ManyToMany(targetEntity="Role", inversedBy="children", fetch="EAGER")
     * @JoinTable(name="Role_Role",
     *      joinColumns={@JoinColumn(name="rol_parent_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="rol_child_id", referencedColumnName="id")}
     *      )
     */
    private $parents;

    /** @ORM\Column(type="string", nullable = false) */
    protected $note;

    /** @ORM\Column(type="datetime", nullable = false) */
    protected $added;
    
    private $parentNames;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->added = new DateTime();
	$this->parents = new ArrayCollection();
	$this->children = new ArrayCollection();
	$this->fromArray($values);
    }
    
    public function __toString() {
	return "{$this->getName()}";
    }
    
    public function getName() {
	return $this->name;
    }
    
    /**
     * @return ArrayCollection
     */
    public function getChildren() {
	return $this->children;
    }

    /**
     * @return ArrayCollection
     */
    public function getParents() {
	return $this->parents;
    }
    
    public function extractParentNames() {
	if (!isset($this->parentNames)) {
	    $pars = $this->getParents();
	    if ($pars instanceof PersistentCollection) {
		 $this->parentNames = $pars->map(function (Role $r) {
		    return $r->getName();
		})->toArray();
	    } else {
		throw new Exceptions\InvalidStateException("Property parents is not properly initialized", 3);
	    }
	}
	return $this->parentNames;
    }

    public function getNote() {
	return $this->note;
    }

    public function getAdded() {
	return $this->added;
    }

    public function setName($name) {
	$this->name = $name;
    }
   
    public function setChildren($children) {
	$this->children = $children;
    }

    public function setParents($parents) {
	if (!$parents instanceof ArrayCollection) {
	    $this->parents = new ArrayCollection($parents);
	} 
	$this->parents = $parents;
    }
    
    public function addParent(Role $parent) {
	$this->parents->add($parent);
    }

    public function setNote($note) {
	$this->note = $note;
    }

    public function setAdded($added) {
	$this->added = $added;
    }
    
    public function getId() {
	return $this->id;
    }

    public function setId($id) {
	$this->id = $id;
    }
}
