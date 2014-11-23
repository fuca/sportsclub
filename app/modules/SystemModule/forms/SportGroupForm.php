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
    \Vodacek\Forms\Controls\DateInput;

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

	$this->addHidden('id');
	$this->addText('name', "systemModule.sportGroupForm.name")
		->addRule(Form::FILLED, "systemModule.sportGroupForm.nameMustFill")
		->setRequired(true);

	$this->addTextArea('description', "systemModule.sportGroupForm.desc", 30, 4);
	
	$this->addText('abbr', "systemModule.sportGroupForm.abbr")
		->addRule(Form::FILLED, "systemModule.sportGroupForm.abbrMustFill")
		->setRequired();
	
	$this->addSelect('parent', "systemModule.sportGroupForm.parent", $this->getSportGroups())
		->setPrompt("systemModule.sportGroupForm.groupSel")
		->addRule(Form::FILLED, "systemModule.sportGroupForm.parentMustFill")
		->setRequired();

	$this->addSelect('priority', "systemModule.sportGroupForm.priority", $this->getPriorities())
		->addRule(Form::FILLED, "systemModule.sportGroupForm.priorityMustFill")
		->setPrompt("systemModule.sportGroupForm.prioritySel");
	
	$this->addSelect('sportType', "systemModule.sportGroupForm.sport", $this->getSportTypes())
		->setPrompt("systemModule.sportGroupForm.sportSel")
		->addRule(Form::FILLED, "systemModule.sportGroupForm.sportMustFill")
		->setRequired(true);
		
	$this->addCheckbox('activity', "systemModule.sportGroupForm.active");
	
	$this->addSubmit('submit', "system.forms.submitButton.label");

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
	    $this->addError($this->presenter->tt("systemModule.sportGroupForm.groupNameExists", null, ["name"=>$values->name]));
	}
    }

}
