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

namespace App\SeasonModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException,
    Vodacek\Forms\Controls\DateInput;

/**
 * Form for creating and updating seasons
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class SeasonApplicationForm extends BaseForm {

    /** @var array of available sport groups */
    private $sportGroups;

    /** @var array of available seasons */
    private $seasons;
    
    /** @var array of users seasons */
    private $users;
    
    /** @var boolean */
    private $creditsActivated;
    
    /** @var boolean */
    private $memberShip;
    
    public function isCredit() {
	return $this->creditsActivated;
    }
    
    public function isMembership() {
	return $this->memberShip;
    }
    
    public function setCreditsActivated($creditsActivated) {
	$this->creditsActivated = $creditsActivated;
    }

    public function setMemberShip($memberShip) {
	$this->memberShip = $memberShip;
    }

    public function getUsers() {
	return $this->users;
    }

    public function setUsers(array $users) {
	$this->users = $users;
    }

        
    public function getSportGroups() {
	return $this->sportGroups;
    }

    public function getSeasons() {
	return $this->seasons;
    }

    public function setSportGroups(array $sportGroups) {
	$this->sportGroups = $sportGroups;
    }

    public function setSeasons(array $seasons) {
	$this->seasons = $seasons;
    }

    public function initialize() {

	$this->addHidden("id");
	if ($this->getMode() == FormMode::CREATE_MODE) {
	    $this->addGroup("Nová přihláška k sezóně");
	} else {
	    $this->addGroup("Úprava přihlášky k sezóně");
	}

	$this->addSelect("owner", "Člen", $this->getUsers())
		->setPrompt("Vyberte člena.. ")
		->addRule(Form::FILLED, "Člen musí být vybrána")
		->setRequired(true);
	
	$this->addSelect("season", "Sezóna", $this->getSeasons())
		->setPrompt("Vyberte sezónu.. ")
		->addRule(Form::FILLED, "Sezóna musí být vybrána")
		->setRequired(true);

	$this->addSelect("sportGroup", "Skupina", $this->getSportGroups())
		->setPrompt("Vyberte skupinu.. ")
		->addRule(Form::FILLED, "Skupina musí být vybrána")
		->setRequired(true);

	if ($this->isCredit()) {
	    $this->addText('creditsRatio', "Podíl reditů")
		    ->addRule(Form::FILLED, "Výše kreditů musí být zadána")
		    ->setRequired(TRUE);
	}

	if ($this->isMemberShip()) {
	    $this->addText('membershipRatio', "Podíl členského příspěvku")
		    ->addRule(Form::FILLED, "Výše členského příspěvku musí být zadána")
		    ->setRequired(TRUE);
	}

	$this->addTextArea("comment", "Poznámka");

	$this->addSubmit('submitButton', "Uložit");
	$this->onSuccess[] = callback($this->presenter, "seasonApplicationFormSubmitHandle");
    }

}
