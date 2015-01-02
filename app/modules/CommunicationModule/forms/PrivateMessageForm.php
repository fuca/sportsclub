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
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Misc\Enum\PrivateMessageRecipientMode;

/**
 * Form for creating and updating seasons
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class PrivateMessageForm extends BaseForm {
    
    /**
     * @var array of users to selectBox
     */
    private $users;
    
    public function getUsers() {
	if (!isset($this->users))
	    throw new Exceptions\InvalidStateException("Property users is not correctly setted up, please use appropriate setter first");
	return $this->users;
    }

    public function setUsers($users) {
	$this->users = $users;
    }
    
    public function initialize() {
	
	$this->addHidden("id");
	if ($this->isCreate()) {
	    $this->addMultiSelect("recipient", "communicationModule.pmForm.multiUsers", $this->getUsers());
	} else {
	    $this->addSelect("recipient", "communicationModule.pmForm.multiUsers", $this->getUsers());
	}
	
	$this->addText("subject", "communicationModule.pmForm.subject");
	
	$this->addTextArea("content", "communicationModule.pmForm.content", 40, 10)
		->addRule(Form::FILLED, "communicationModule.pmForm.contentMustFill")
		->setRequired("communicationModule.pmForm.contentMustFill");
	
	$this->addSubmit("submitButton", "communicationModule.pmForm.submitButton.label");
	$this->onSuccess[] = callback($this->presenter, "privateMessageFormSuccess");
    }
    
}
