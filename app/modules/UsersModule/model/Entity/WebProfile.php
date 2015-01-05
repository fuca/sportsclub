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
    \Doctrine\ORM\Mapping\ManyToOne,
    \Doctrine\ORM\Mapping\JoinColumn,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Enum\WebProfileStatus,
    \App\Model\Misc\EntityMapperTrait;

/**
 * ORM persistable entity representing user's web profile
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @ORM\Entity
 */
class WebProfile extends BaseEntity {
        use EntityMapperTrait;
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $personalLikes;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $personalDislikes;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $personalInterests;
    
    /** @ORM\Column(type="integer", nullable=true) */
    protected $jerseyNumber;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $equipment;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $favouriteBrand;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $favouriteClub;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $sportExperience;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $howGotThere;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $aditionalInfo;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $picture;
    
    /** @ORM\Column(type="datetime", nullable=false) */
    protected $updated;
    
    /** @ORM\Column(type="WebProfileStatus") */
    protected $status;
    
    /** @ORM\Column(type="boolean", nullable=true) */
    protected $publish;
    
    /** 
     * @ManyToOne(targetEntity="User")
     * @JoinColumn(name="editor_id", referencedColumnName = "id", nullable=true)
     */
    protected $editor;
    
    /** @ORM\Column(type="string", nullable=true) */
    protected $signature;
    
    public function __construct(array $values = []) {
	parent::__construct();
	$this->status = WebProfileStatus::BAD;
	$this->fromArray($values);
    }
    
    public function getId() {
	return $this->id;
    }

    public function getPersonalLikes() {
	return $this->personalLikes;
    }

    public function getPersonalDislikes() {
	return $this->personalDislikes;
    }

    public function getPersonalInterests() {
	return $this->personalInterests;
    }

    public function getJerseyNumber() {
	return $this->jerseyNumber;
    }

    public function getEquipment() {
	return $this->equipment;
    }

    public function getFavouriteBrand() {
	return $this->favouriteBrand;
    }

    public function getFavouriteClub() {
	return $this->favouriteClub;
    }

    public function getSportExperience() {
	return $this->sportExperience;
    }

    public function getHowGotThere() {
	return $this->howGotThere;
    }

    public function getAditionalInfo() {
	return $this->aditionalInfo;
    }

    public function getUpdated() {
	return $this->updated;
    }

    public function getEditor() {
	return $this->editor;
    }

    public function getSignature() {
	return $this->signature;
    }

    public function setPersonalLikes($personalLikes) {
	$this->personalLikes = $personalLikes;
    }

    public function setPersonalDislikes($personalDislikes) {
	$this->personalDislikes = $personalDislikes;
    }

    public function setPersonalInterests($personalInterests) {
	$this->personalInterests = $personalInterests;
    }

    public function setJerseyNumber($jerseyNumber) {
	$this->jerseyNumber = $jerseyNumber;
    }

    public function setEquipment($equipment) {
	$this->equipment = $equipment;
    }

    public function setFavouriteBrand($favouriteBrand) {
	$this->favouriteBrand = $favouriteBrand;
    }

    public function setFavouriteClub($favouriteClub) {
	$this->favouriteClub = $favouriteClub;
    }

    public function setSportExperience($sportExperience) {
	$this->sportExperience = $sportExperience;
    }

    public function setHowGotThere($howGotThere) {
	$this->howGotThere = $howGotThere;
    }

    public function setAditionalInfo($aditionalInfo) {
	$this->aditionalInfo = $aditionalInfo;
    }

    public function setUpdated($updated) {
	$this->updated = $updated;
    }

    public function setEditor($editor) {
	$this->editor = $editor;
    }
    
    public function getStatus() {
	return $this->status;
    }

    public function setStatus($status) {
	$this->status = $status;
    }

    public function setSignature($signature) {
	$this->signature = $signature;
    }
    
    public function getPicture() {
	return $this->picture;
    }

    public function setPicture($picture) {
	if (($picture instanceof \Nette\Http\FileUpload && $picture == "") || empty($picture)) 
	    return;
	$this->picture = $picture;
    }
    
    public function getPublish() {
	return $this->publish;
    }

    public function setPublish($publish) {
	$this->publish = $publish;
    }
}
