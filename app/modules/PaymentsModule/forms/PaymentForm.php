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

namespace App\PaymentsModule\Forms;

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
final class PaymentForm extends BaseForm {
    
    const 
	PAYMENT_OWNER_TYPE_SELECT_ID	= "ownerType",
	OWNER_TYPE_SINGLE	= "single",
	OWNER_TYPE_GROUP	= "sportGroup",
	OWNER_TYPE_SELECT	= "owners";
	    

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

    public function getState() {
	return PaymentStatus::getOptions();
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

    public function getOwnersSelect() {
	return PaymentOwnerType::getOptions();
    }

    public function initialize() {
	$this->addHidden('id');

	$osel = null;
	if ($this->isCreate()) {
	    $osel = $this->addSelect(self::PAYMENT_OWNER_TYPE_SELECT_ID, "Typ zadání platby", $this->getOwnersSelect());
	
	    $osel->addCondition(Form::EQUAL, self::OWNER_TYPE_SINGLE)
		    ->toggle("single-owner");
	
	    $osel->addCondition(Form::EQUAL, self::OWNER_TYPE_GROUP)
		    ->toggle("group-owner");
	    
	    $osel->addCondition(Form::EQUAL, self::OWNER_TYPE_SELECT)
		    ->toggle("multi-owner");
	}

	if ($this->isUpdate()) {
	    $this->addSelect("owner", "Člen", $this->getUsers())
		->setPrompt("Vyberte člena.. ")
		->setOption("id", "single-owner")
		->setRequired();
	} else {
	$this->addSelect("owner", "Člen", $this->getUsers())
		->setPrompt("Vyberte člena.. ")
		->setOption("id", "single-owner")
		->addConditionOn($osel, Form::NOT_EQUAL, null)
		->addConditionOn($osel, Form::EQUAL, PaymentOwnerType::SINGLE)
		->setRequired();

	$this->addSelect("sportGroup", "Skupina", $this->getSportGroups())
		->setPrompt("Vyberte skupinu.. ")
		->setOption("id", "group-owner")
		->addConditionOn($osel, Form::NOT_EQUAL, null)
		->addConditionOn($osel, Form::EQUAL, PaymentOwnerType::GROUP)
		->setRequired();

	$this->addMultiSelect("owners", "Členové", $this->getUsers())
		->setOption("id", "multi-owner")
		->addConditionOn($osel, Form::NOT_EQUAL, null)
		->addConditionOn($osel, Form::EQUAL, PaymentOwnerType::SELECT)
		->setRequired();
	}
	
	$this->addText("subject", "Předmět")
		->addRule(Form::FILLED, "Předmět musí být zadán")
		->setRequired(true);

	$this->addText("amount", "Částka")
		->addRule(Form::FILLED, "Částka musí být zadána")
		->setRequired(true);

	$this->addText("vs", "Variabilní symbol");

	$this->addDate("dueDate", "Datum splatnosti", DateInput::TYPE_DATE)
		->setDefaultValue(new DateTime("+ 1 month"))
		->addRule(Form::FILLED, "Datum splatnosti musí být zadáno")
		->setRequired(true);
	
	$this->addSelect("season", "Sezóna", $this->getSeasons())
		->setPrompt("Vyberte sezonu.. ");

	if ($this->isUpdate()) {
	    $this->addSelect("status", "Stav", $this->getState())
		    ->setRequired(true);

	    $this->addSelect("editor", "Editoval", $this->getUsers())
		    ->setDisabled(true);

	    $this->addDate("orderedDate", "Zadáno", DateInput::TYPE_DATE);
	}

	$this->addCheckbox("protectedNoteToggle", "Přidat poznámku")
		->addCondition(Form::EQUAL, true)
		->toggle("protected-note");

	$this->addTextArea("protectedNote", "Poznámka")
		->setOption("id", "protected-note");

	$this->addCheckbox("publicNoteToggle", "Napsat poznámku uživateli")
		->addCondition(Form::EQUAL, true)
		->toggle("public-note");

	$this->addTextArea("publicNote", "Pzn. pro uživ.")
		->setOption("id", "public-note");

	$this->addSubmit("submitButton", "Uložit");
	$this->onSuccess[] = callback($this->presenter, "paymentFormSubmitHandle");
    }

}
