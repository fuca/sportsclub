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
    Doctrine\ORM\Mapping\OneToOne,
    Doctrine\ORM\Mapping\JoinColumn,
    Doctrine\ORM\Mapping\JoinTable,
    Doctrine\ORM\Mapping\ManyToMany,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\WebProfileStatus,
    Nette\Security\IIdentity,
    \Nette\DateTime,
    \App\Model\IIdentifiable,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity representing real User
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class User extends BaseEntity implements IIdentity, IIdentifiable {
        use EntityMapperTrait;
	
    // in case of postgres db
    
    // @ORM\Id
    // @ORM\Column(type="integer") 
    // @ORM\GeneratedValue(strategy="SEQUENCE")
    // @ORM\SequenceGenerator(sequenceName="primary_key_seq", initialValue=1, allocationSize=100)

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /** @ORM\Column(type="string", nullable=true) */
    protected $password;

    /** @ORM\Column(type="boolean") */
    protected $passwordChangeRequired;

    /** @ORM\Column(type="string") */
    protected $name;

    /** @ORM\Column(type="string") */
    protected $surname;

    /** @ORM\Column(type="string", unique = true) */
    protected $birthNumber;

    /** @ORM\Column(type="boolean") */
    protected $active;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected $lastLogin;

    /** @ORM\Column(type="string") */
    protected $nick;

    /** @ORM\Column(type="WebProfileStatus") */
    protected $profileStatus;

    /** @ORM\Column(type="string", nullable=true) */
    protected $leagueId;

    /** @ORM\Column(type="datetime", nullable=true) */
    protected $created;

    /**
     * @OneToOne(targetEntity = "Contact", cascade={"all"}, orphanRemoval=true, fetch="EAGER")
     * @JoinColumn(name = "contact_fk", referencedColumnName = "id", nullable = true, onDelete="CASCADE")
     */
    protected $contact;

    /**
     * @OneToOne(targetEntity = "WebProfile", cascade={"all"}, orphanRemoval=true)
     * @JoinColumn(unique = true, name = "profile_fk", referencedColumnName = "id", nullable = true, onDelete="CASCADE")
     */
    protected $webProfile;

    /** @ORM\Column(type="CommentMode") */
    protected $commentMode;

    /**
     * ONE TO MANY
     * @ManyToMany(targetEntity="Comment", cascade={"remove"})
     * @JoinTable(name="Comment_User",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="comment_id", referencedColumnName="id", unique=true)})
     */
    protected $comments;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->created = new DateTime();
	$this->profileStatus = WebProfileStatus::BAD;
	$this->commentMode = CommentMode::ALLOWED;
	$this->active = false;
	$this->passwordChangeRequired = true;
	$this->fromArray($values);
    }
    
    protected $roles;

    public function setRoles(array $roles) {
	$this->roles = $roles;
    }

    /* implementation of IIdentity */

    public function getId() {
	return $this->id;
    }

    public function getRoles() {
	return [];
    }

    public function getPassword() {
	return $this->password;
    }

    public function getPasswordChangeRequired() {
	return $this->passwordChangeRequired;
    }

    public function getName() {
	return $this->name;
    }

    public function getSurname() {
	return $this->surname;
    }

    public function getBirthNumber() {
	return $this->birthNumber;
    }

    public function getActive() {
	return $this->active;
    }

    public function getLastLogin() {
	return $this->lastLogin;
    }

    public function getNick() {
	return $this->nick;
    }

    public function getProfileStatus() {
	return $this->profileStatus;
    }

    public function getLeagueId() {
	return $this->leagueId;
    }

    public function getContact() {
	return $this->contact;
    }

    public function getWebProfile() {
	return $this->webProfile;
    }

    public function getCommentMode() {
	return $this->commentMode;
    }

    public function getComments() {
	return $this->comments;
    }

    public function setPassword($password) {
	$this->password = $password;
	return $this;
    }

    public function setPasswordChangeRequired($passwordChangeRequired) {
	$this->passwordChangeRequired = $passwordChangeRequired;
    }

    public function setName($name) {
	$this->name = $name;
    }

    public function setSurname($surname) {
	$this->surname = $surname;
    }

    public function setBirthNumber($birthNumber) {
	$this->birthNumber = $birthNumber;
    }

    public function setActive($active) {
	$this->active = $active;
    }

    public function setLastLogin($lastLogin) {
	$this->lastLogin = $lastLogin;
    }

    public function setNick($nick) {
	$this->nick = $nick;
    }

    public function setProfileStatus($profileStatus) {
	$this->profileStatus = $profileStatus;
    }

    public function setLeagueId($leagueId) {
	$this->leagueId = $leagueId;
    }

    public function setContact(Contact $contact) {
	$this->contact = $contact;
    }

    public function setWebProfile($webProfile) {
	$this->webProfile = $webProfile;
    }

    public function setCommentMode($commentMode) {
	$this->commentMode = $commentMode;
    }

    public function setComments($comments) {
	$this->comments = $comments;
    }

    public function getCreated() {
	return $this->created;
    }

    public function setCreated($created) {
	$this->created = $created;
    }
    
    public function setId($id) {
	$this->id = $id;
    }

    public function __toString() {
	return "{$this->getSurname()} {$this->getName()} ({$this->getId()})";
    }


}
