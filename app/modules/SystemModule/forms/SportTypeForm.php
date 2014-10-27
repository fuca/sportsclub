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
    \App\Services\Exceptions\DuplicateEntryException;

/**
 * Form for creating and updating sport types
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class SportTypeForm extends BaseForm {

    /**
     * @var array of sport type ico paths
     */
    private $images;

    public function getImages() {
	if (!isset($this->images))
	    $this->images = [];
	return $this->images;
    }

    public function setImages($images) {
	$this->images = $images;
    }

    public function initialize() {

	if ($this->getMode() == FormMode::CREATE_MODE) {
	    $this->addGroup("systemModule.sportTypeForm.newGroup");    
	} else {
	    $this->addGroup("systemModule.sportTypeForm.editGroup");
	}
	
	$this->addHidden('id');
	$this->addText('name', "systemModule.sportTypeForm.name")
		->addRule(Form::FILLED, "systemModule.sportTypeForm.nameMustFill")
		->setRequired(true);

//	$this->addImageSelectBox('image', 'Ikona', $this->getImages())
//		//->addRule(Form::FILLED, "Ikona musí být vybrána")
//		->setPrompt("Vyberte ikonu..");

	$this->addTextArea('note', "systemModule.sportTypeForm.note", 30, 4);

	$this->addSubmit('submit', "system.forms.submitButton.label");

	$this->onSuccess[] = callback($this, 'sportTypeFormSubmitted');
    }

    public function sportTypeFormSubmitted(Form $form) {

	$values = $form->getValues();

	try {
	    switch ($this->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->presenter->createSportType($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->presenter->updateSportType($values);
		    break;
	    }
	} catch (DuplicateEntryException $ex) {
	    $name = $values->name;
	    // TODO LOG
	    $this->addError("Sport type with name {$name} already exists");
	}
    }

}
