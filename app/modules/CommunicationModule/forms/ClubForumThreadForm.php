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

namespace App\CommunicationModule\Forms;

use \App\Forms\BaseForm,
    \Nette\DateTime,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException,
    \Vodacek\Forms\Controls\DateInput,
    \App\Model\Misc\PaymentStatus,
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\PaymentOwnerType;

/**
 * Form for creating and updating forum threads
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class ClubForumThreadForm extends BaseForm {
    
    /**
     * @var array of users to selectBox
     */
    private $users;
    
    /**
     * @var array of forum to selectBox
     */
    private $forums;
    
    public function getUsers() {
	if (!isset($this->users))
	    throw new Exceptions\InvalidStateException("Property users is not correctly setted up, please use appropriate setter first");
	return $this->users;
    }
    
    public function getForums() {
	if (!isset($this->forums))
	    throw new Exceptions\InvalidStateException("Property forums is not correctly setted up, please use appropriate setter first");
	return $this->forums;
    }

    public function setUsers($users) {
	$this->users = $users;
    }
    
    public function setForum($forum) {
	$this->forums = $forum;
    }
    
    public function getCommentModes() {
	return CommentMode::getOptions();
    }
    
    public function initialize() {
	
	$this->addHidden("id");
	$this->addHidden("alias");
	$this->addHidden("forum");
	
	$this->addText("title", "communicationModule.forThrForm.title")
		->addRule(Form::FILLED, "communicationModule.forThrForm.titleMustFill")
		->setRequired("communicationModule.forThrForm.titleMustFill");
	
	$this->addTextArea("description", "communicationModule.forThrForm.description", 40, 10)
		->addRule(Form::FILLED, "communicationModule.forThrForm.descriptionMustFill")
		->setRequired("communicationModule.forThrForm.descriptionMustFill");

	$this->addSubmit("submitButton", "system.forms.submitButton.label");
	$this->onSuccess[] = callback($this->presenter, "forumThreadFormSuccess");
    }
    
}
