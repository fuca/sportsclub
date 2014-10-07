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

namespace App\ForumModule\Forms;

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
 * Form for creating and updating seasons
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class ForumForm extends BaseForm {
    
    /**
     * @var array of users to selectBox
     */
    private $users;
    
    /**
     * @var array of sport groups to selectBox
     */
    private $sportGroups;
    
    public function getUsers() {
	if (!isset($this->users))
	    throw new Exceptions\InvalidStateException("Property users is not correctly setted up, please use appropriate setter first");
	return $this->users;
    }

    public function getSportGroups() {
	if (!isset($this->sportGroups))
	    throw new Exceptions\InvalidStateException("Property sportGroups is not correctly setted up, please use appropriate setter first");
	return $this->sportGroups;
    }

    public function setUsers($users) {
	$this->users = $users;
    }

    public function setSportGroups($sportGroups) {
	$this->sportGroups = $sportGroups;
    }
    
    public function getCommentModes() {
	return CommentMode::getOptions();
    }
    
    public function initialize() {
	
	$this->addHidden("id");
	$this->addHidden("alias");
	$this->addText("title", "Název")
		->addRule(Form::FILLED, "Název fóra musí být zadán")
		->setRequired("Název fóra musí být zadán");
	$this->addTextArea("description", "Popis", 40, 10)
		->addRule(Form::FILLED, "Popis fóra musí být zadán")
		->setRequired("Popis fóra musí být zadán");
	$this->addSelect("imgName", "Ikona", []); // TODO 
	$this->addSelect("commentMode", "Komentáře", $this->getCommentModes());
	$this->addCheckboxList("groups", "Skupiny", $this->getSportGroups());
	if ($this->isUpdate()) {
	    $this->addDate("lastActivity", "Poslední aktivita", DateInput::TYPE_DATE);
	    $this->addSelect("author", "Autor", $this->getUsers());
	    $this->addSelect("editor", "Editor", $this->getUsers());
	}
	$this->addSubmit("submitButton");
	$this->onSuccess[] = callback($this->presenter, "forumFormSubmitted");
    }
    
}
