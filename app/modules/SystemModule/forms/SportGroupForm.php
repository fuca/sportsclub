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
    Vodacek\Forms\Controls\DateInput;

/**
 * Form for creating and updating sport types
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class SportGroupForm extends BaseForm {

    /**
     * @var array of sport groups
     */
    private $sportGroups;
    
    
    /**
     * @var array of sport types
     */
    private $sportTypes;
    
    /**
     * @var array of priorities
     */
    private $priorities;
    
    
    public function getSportTypes() {
	return $this->sportTypes;
    }

    public function getPriorities() {
	return $this->priorities;
    }

    public function setSportTypes($sportTypes) {
	$this->sportTypes = $sportTypes;
    }

    public function setPriorities($priorities) {
	$this->priorities = $priorities;
    }
    
    public function getSportGroups() {
	return $this->sportGroups;
    }

    public function setSportGroups($sportGroups) {
	$this->sportGroups = $sportGroups;
    }

    public function initialize() {

	if ($this->getMode() == FormMode::CREATE_MODE) {
	    $this->addGroup('Nová skupina');
	} else {
	    $this->addGroup('Editace skupiny');
	}
	

	$this->addHidden('id');
	$this->addText('name', 'Název')
		->addRule(Form::FILLED, "Název musí výt vyplněn")
		->setRequired(true);

	$this->addTextArea('description', "Popis", 30, 4)
		->addRule(Form::FILLED, "Pole popis je povinné")
		->setRequired(TRUE);
	
	$this->addText('abbr', "Zkratka")
		->addRule(Form::FILLED, "Pole zkratka nesmí být prázdné");
	
	$this->addSelect('parent', 'Nadřazená skupina', $this->getSportGroups())
		->setPrompt("Vyberte skupinu..");
	
	$this->addDate("appDate", "Deadline přihlášek", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "Datum deadlinu přihlášek musí být vybrán");

	$this->addSelect('priority', 'Priorita', $this->getPriorities())
		->addRule(Form::FILLED, "Priorita musí být vybrána")
		->setPrompt("Vyberte prioritu..");
	
	$this->addSelect('sportType', 'Druh sportu', $this->getSportTypes())
		->setPrompt("Vyberte sport..")
		->addRule(Form::FILLED, "Typ sportu musí být vyplněn")
		->setRequired(true);
		
	$this->addCheckbox('activity', 'Aktivní');
	
	$this->addSubmit('submit', 'Uložit');

	$this->onSuccess[] = callback($this, 'sportTypeFormSubmitted');
    }

    public function sportTypeFormSubmitted(Form $form) {

	$values = $form->getValues();
	try {
	    switch ($this->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->presenter->createSportGroup($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->presenter->updateSportGroup($values);
		    break;
	    }
	} catch (DuplicateEntryException $ex) {
	    $name = $values->name;
	    // TODO LOG
	    $this->addError("Sport type with name {$name} already exists");
	}
    }

}
