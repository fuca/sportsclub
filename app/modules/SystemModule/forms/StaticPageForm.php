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

namespace App\SystemModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException,
    \Vodacek\Forms\Controls\DateInput,
    \App\Model\Misc\Enum\StaticPageStatus,
    \App\Model\Misc\Enum\CommentMode;

/**
 * Form for creating and updating static pages
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class StaticPageForm extends BaseForm {

    /**
     * @var array of sport groups
     */
    private $sportGroups;
    
    /**
     * @var array of users
     */
    private $users;
    
    /**
     * @var array of static pages
     */
    private $pages;
    
    public function getSportGroups() {
	return $this->sportGroups;
    }

    public function setSportGroups($sportGroups) {
	$this->sportGroups = $sportGroups;
    }
    
    public function getUsers() {
	return $this->users;
    }

    public function setUsers($users) {
	$this->users = $users;
    }
    
    function getPages() {
	return $this->pages;
    }

    function setPages($pages) {
	$this->pages = $pages;
    }
    
    public function getStates() {
	return StaticPageStatus::getOptions();
    }
    
    public function getCommentModes() {
	return CommentMode::getOptions();
    }

    public function initialize() {
	parent::initialize();
	
	$this->addHidden('id');
	
	if ($this->isCreate()) {
	    $this->addHidden('abbr');    
	} else {
	    $this->addText('abbr', "systemModule.staticPageForm.abbr")
		->addRule(Form::FILLED, "systemModule.staticPageForm.abbrMustFill")
		->setRequired();
	}   
	
	$this->addText('title', "systemModule.staticPageForm.title")
		->addRule(Form::FILLED, "systemModule.staticPageForm.titleMustFill")
		->setRequired("systemModule.staticPageForm.titleMustFill");
	
//	$this->addSelect('parent', "systemModule.staticPageForm.parent", 
//		$this->getPages())
//		->setPrompt("systemModule.staticPageForm.pageSel")
//		->addRule(Form::FILLED, "systemModule.staticPageForm.parentMustFill")
//		->setRequired("systemModule.staticPageForm.parentMustFill");
	
	$this->addSelect("group", "systemModule.staticPageForm.group",
		$this->getSportGroups());
	
	
	$this->addSelect("status", "systemModule.staticPageForm.status", 
		$this->getStates());
	
	$this->addSelect("commentMode", "systemModule.staticPageForm.commentMode",
		$this->getCommentModes())
		->setDefaultValue(CommentMode::RESTRICTED);
	
	if ($this->isUpdate()) {
	    $this->addDate("updated", "systemModule.staticPageForm.updated", 
		    DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "systemModule.staticPageForm.updatedMustFill")
		->setRequired("systemModule.staticPageForm.updatedMustFill");
	    
	    $this->addSelect("editor", "systemModule.staticPageForm.editor",
		    $this->getUsers())
		    ->setDisabled();
	}
	
	$this->addTextArea('content', "systemModule.staticPageForm.content", 55, 20);
	
	$this->addSubmit('submit', "system.forms.submitButton.label");

	$this->onSuccess[] = $this->presenter->staticPageFormSubmitted;
    }
}
