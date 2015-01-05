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

namespace App\MotivationModule\Forms;

use \App\Forms\BaseForm,
    \Nette\DateTime,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException,
    \Vodacek\Forms\Controls\DateInput,
    \App\Model\Misc\Enum\MotivationEntryType;

/**
 * Form for creating and updating motivation entry
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class MotivationEntryForm extends BaseForm {
    
    const MULTI_OWNER_ID = "owners";

    /** @var array of available seasons */
    private $seasons;

    /** @var array of users seasons */
    private $users;

    public function getSeasons() {
	return $this->seasons;
    }

    public function getUsers() {
	return $this->users;
    }

    public function getTypes() {
	return MotivationEntryType::getOptions();
    }

    public function setSeasons($seasons) {
	$this->seasons = $seasons;
    }

    public function setUsers($users) {
	$this->users = $users;
    }

    public function initialize() {
	
	$this->addHidden('id');

	if ($this->isCreate()) {
	    $this->addMultiSelect(self::MULTI_OWNER_ID, "motivationModule.entryForm.member", $this->getUsers(), 25)
		    ->setOption("id", "multi-owner")
		    ->setRequired();
	} else {
	    $this->addSelect("owner", "motivationModule.entryForm.member", $this->getUsers())
		    ->setRequired();
	}
	
	$this->addText("subject", "motivationModule.entryForm.subject")
		->addRule(Form::FILLED, "Předmět musí být zadán")
		->setRequired(true);

	$this->addText("amount", "motivationModule.entryForm.amount")
		->addRule(Form::FILLED, "Částka musí být zadána")
		->setRequired(true);
	
	$this->addSelect("season", "motivationModule.entryForm.season", $this->getSeasons())
		->setPrompt("motivationModule.entryForm.seasonSel");

	$this->addSelect("type", "motivationModule.entryForm.type", $this->getTypes())
		    ->setRequired(true);
	
	if ($this->isUpdate()) {    
	    $this->addSelect("editor", "motivationModule.entryForm.editor", $this->getUsers())
		    ->setDisabled(true);
	}

	$this->addSubmit("submitButton", "system.forms.submitButton.label");
	$this->onSuccess[] = callback($this->presenter, "motEntryFormSuccessHandle");
    }

}
