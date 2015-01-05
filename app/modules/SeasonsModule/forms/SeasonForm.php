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
final class SeasonForm extends BaseForm {

    public function initialize() {

	$this->addHidden("id");
	$this->addText("label", "Popis");
	$this->addDate("dateSince", "Začátek", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "Datum začátku sezóny musí být zadán");
	
	$this->addDate("dateTill", "Konec", DateInput::TYPE_DATE)
		->addRule(Form::FILLED, "Datum konce sezóny musí být zadán");
	
	$this->addCheckbox('current', "Aktuální");
	
	$this->addTextArea("comment", "Poznámka", null, 7);

	$this->addSubmit('submitButton', "Uložit");
	$this->onSuccess[] = callback($this->presenter, "seasonFormSubmitHandle");
    }
}
