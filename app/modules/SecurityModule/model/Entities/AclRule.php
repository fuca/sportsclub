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
    Doctrine\ORM\Mapping\UniqueConstraint,
    Doctrine\ORM\Mapping\JoinColumn,
    Doctrine\ORM\Mapping\ManyToOne,
    Doctrine\ORM\Mapping\Id,
    Doctrine\ORM\Mapping\GeneratedValue,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\AclMode,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity representing acl rule
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 * @ORM\Table(name="AclRule", 
 * uniqueConstraints={
 *  @UniqueConstraint(name="unique_aclRule", columns={"role_fk", "resource"})})
 */
class AclRule extends BaseEntity {
    
    use EntityMapperTrait;
    
    /**
     * @Id
     * @ORM\Column(type="bigint")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="Role", fetch = "EAGER")
     * @JoinColumn(name="role_fk", referencedColumnName="id", nullable = false)
     */
    protected $role;

    /**
     * @ORM\Column(type="string")
     */
    protected $resource;

    /**
     * @ORM\Column(type="string")
     */
    protected $privilege;

    /**
     * @ORM\Column(type="AclMode")
     */
    protected $mode;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getRole() {
	return $this->role;
    }

    public function getResource() {
	return $this->resource;
    }

    public function getPrivilege() {
	return $this->privilege;
    }

    public function getMode() {
	return $this->mode;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setRole($role) {
	$this->role = $role;
    }

    public function setResource($resource) {
	$this->resource = $resource;
    }

    public function setPrivilege($privilege) {
	$this->privilege = $privilege;
    }

    public function setMode($mode) {
	$this->mode = $mode;
    }

}
