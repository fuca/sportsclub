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
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\EntityMapperTrait,
    \Nette\DateTime;

/**
 * ORM persistable entity private message
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class PrivateMessage extends BaseEntity {
    
    use EntityMapperTrait;
  
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** 
     * @ORM\Column(type="datetime", nullable = false) 
     */
    protected $sent;
    
    /** 
     * @ORM\Column(type="string", nullable = false) 
     */
    protected $subject;
    
    /** 
     * @ORM\Column(type="string", nullable = false) 
     */
    protected $content;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->sent = new DateTime();
	$this->fromArray($values);
    }

    public function getId() {
	return $this->id;
    }

    public function getSent() {
	return $this->sent;
    }

    public function getSubject() {
	return $this->subject;
    }

    public function getContent() {
	return $this->content;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setSent($sent) {
	$this->sent = $sent;
    }

    public function setSubject($subject) {
	$this->subject = $subject;
    }

    public function setContent($content) {
	$this->content = $content;
    }

       public function __toString() {
	return "{$this->getId()}";
    }
    
}
