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
    Vodacek\Forms\Controls\DateInput,
    \App\Model\Misc\PaymentStatus,
    \App\Model\Misc\Enum\PaymentOwnerType,
    \App\Model\Misc\Enum\EventType,
    \App\Model\Misc\Enum\EventVisibility,
    \App\Model\Misc\Enum\CommentMode;

/**
 * Form for creating and updating events
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class EventForm extends BaseForm {

    /** @var Sport group list */
    private $sportGroups;

    /** @var users list */
    private $users;

    private function getTypes() {
	return EventType::getOptions();
    }

    private function getVisibility() {
	return EventVisibility::getOptions();
    }

    private function getCommentModes() {
	return CommentMode::getOptions();
    }

    private function getSportGroups() {
	if (!isset($this->sportGroups)) {
	    throw new \Nette\InvalidStateException("Property sportGroups is not setted up correctly, use appropriate setter first");
	}
	return $this->sportGroups;
    }

    public function getUsers() {
	return $this->users;
    }

    public function setUsers($users) {
	$this->users = $users;
    }

    public function setSportGroups($groups) {
	$this->sportGroups = $groups;
    }

    public function initialize() {
	$this->addHidden("id");
	$this->addHidden("alias");
	$this->addSelect("eventType", "Typ", $this->getTypes())
		->addRule(Form::FILLED, "Typ musí být zadán")
		->setRequired("Typ musí být zadán");

	$this->addText("title", "Titulek")
		->addRule(Form::FILLED, "Titulek musí být zadán")
		->setRequired("Titulek musí být zadán");

	$this->addTextArea("description", "Popis", 60, 10)
		->addRule(Form::FILLED, "Popis musí být zadán")
		->setRequired("Popis musí být zadán");

	$this->addDate("takePlaceSince", "Od", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "Počátek akce musí být zadán")
		->setRequired("Počátek akce musí být zadán");

	$this->addDate("takePlaceTill", "Do", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "Konec akce musí být zadán")
		->setRequired("Konec akce musí být zadán");

	$this->addDate("confirmUntil", "Potvrdit do", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "Čas potvrzení musí být zadán")
		->setRequired("Čas potvrzení musí být zadán");

	$this->addSelect("visibility", "Viditelnost", $this->getVisibility())
		->addRule(Form::FILLED, "Viditelnost musí být vybrána")
		->setRequired("Viditelnost musí být vybrána");

	$this->addSelect("commentMode", "Komentáře", $this->getCommentModes())
		->addRule(Form::FILLED, "Komentáře musí být vybrány")
		->setRequired("Komentáře musí být vybrány");

	if ($this->isUpdate()) {
	    $this->addSelect("author", "Autor", $this->getUsers());
	    $this->addSelect("editor", "Poslední změna", $this->getUsers());
	}

	$this->addCheckboxList("groups", "Skupiny", $this->getSportGroups());

	$this->addSubmit("submitButton", "Uložit");

	$this->onSuccess[] = callback($this->presenter, "eventFormSubmittedHandle");
    }

}
