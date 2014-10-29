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
    \App\Model\Misc\Enum\PaymentStatus,
    \App\Model\Misc\Enum\PaymentOwnerType;

/**
 * Form for creating and updating seasons
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class MotivationTaxForm extends BaseForm {
	    

    /** @var array of available sport groups */
    private $sportGroups;

    /** @var array of available seasons */
    private $seasons;

    /** @var array of users seasons */
    private $users;

    public function getSportGroups() {
	return $this->sportGroups;
    }

    public function getSeasons() {
	return $this->seasons;
    }

    public function getUsers() {
	return $this->users;
    }

    public function setSportGroups($sportGroups) {
	$this->sportGroups = $sportGroups;
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
	    $this->addGroup("motivationModule.taxForm.groupNew");
	} else {
	    $this->addGroup("motivationModule.taxForm.groupEdit");
	}

	$this->addSelect("season", "motivationModule.taxForm.season", $this->getSeasons())
		->setPrompt("motivationModule.taxForm.seasonSel")
		->addRule(Form::FILLED, "motivationModule.taxForm.seasonMustFill")
		->setRequired("motivationModule.taxForm.seasonMustFill");
	
	$this->addSelect("sportGroup", "motivationModule.taxForm.group", $this->getSportGroups())
		->setPrompt("motivationModule.taxForm.groupSel")
		->addRule(Form::FILLED, "motivationModule.taxForm.groupMustFill")
		->setRequired("motivationModule.taxForm.groupMustFill");
	
	$this->addText("credit", "motivationModule.taxForm.credit")
		->addRule(Form::NUMERIC, "motivationModule.taxForm.creditMustNumeric")
		->addRule(Form::FILLED, "motivationModule.taxForm.creditMustFill")
		->setRequired(true);
	
//	$this->addCheckbox("publicNoteToggle", "motivationModule.taxForm.publicNoteToggle")
//		->addCondition(Form::EQUAL, true)
//		->toggle("public-note");

	$this->addTextArea("publicNote", "motivationModule.taxForm.publicNote")
		->setOption("id", "public-note");

	if ($this->isUpdate()) {
	    $this->addSelect("editor", "motivationModule.taxForm.editor", $this->getUsers())
		    ->setDisabled(true);
	    
	    $this->addDate("updated", "motivationModule.taxForm.update", DateInput::TYPE_DATETIME_LOCAL);
	}

	$this->addSubmit("submitButton", "system.forms.submitButton.label");
	$this->onSuccess[] = callback($this->presenter, "motTaxFormSuccessHandle");
    }

}
