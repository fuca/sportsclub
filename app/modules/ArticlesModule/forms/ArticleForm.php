<?php

/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik@gmail.com>
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

namespace App\ArticlesModule\Forms;

use \App\Forms\BaseForm,
    \Nette\DateTime,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException,
    \Vodacek\Forms\Controls\DateInput,
    \App\Model\Misc\PaymentStatus,
    \App\Model\Misc\Enum\PaymentOwnerType,
    \App\Model\Misc\Enum\EventType,
    \App\Model\Misc\Enum\EventVisibility,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\ArticleStatus;

/**
 * Form for creating and updating articles
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class ArticleForm extends BaseForm {
    
    /**
     * @var sport groups select list
     */
    private $sportGroups;
    
    /**
     * @var users select list
     */
    private $users;
    
    public function getStates() {
	return ArticleStatus::getOptions();
    }
    
    public function getCommentModes() {
	return CommentMode::getOptions();
    }
    
    public function getSportGroups() {
	return $this->sportGroups;
    }
    
    public function getUsers() {
	return $this->users;
    }
    
    public function setSportGroups($sportGroups) {
	$this->sportGroups = $sportGroups;
    }

    public function setUsers($users) {
	$this->users = $users;
    }   
    
    public function initialize() {
	$this->addHidden("id");
	$this->addHidden("alias");
	$this->addHidden("counter");
	
	$this->addText("title", "Titulek", 45)
	    ->addRule(Form::FILLED, "Titulek musí být zadán")
	    ->setRequired("Titulek musí být zadán");
	
	$this->addTextArea("content", "Obsah", 45, 35)
	    ->addRule(Form::FILLED, "Obsah musí být zadán")
	    ->setRequired("Obsah musí být zadán");
	
	$this->addUpload("picture", "Obrázek");

	$this->addCheckbox("highlight", "Zvýraznit"); 
	$this->addSelect("status", "Stav", $this->getStates()); 
	$this->addSelect("commentMode", "Komentáře", $this->getCommentModes());
	$this->addCheckboxList("groups", "Skupiny", $this->getSportGroups()); 
	
	if ($this->isUpdate()) {
	    $this->addSelect("author", "Autor", $this->getUsers());
	    $this->addSelect("editor", "Poslední změna", $this->getUsers());
	}
	
	$this->addSubmit("submit", "system.forms.submitButton.label");
	$this->onSuccess[] = callback($this->parent, "articleFormSubmitted");
    }
}
