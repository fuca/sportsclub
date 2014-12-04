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

namespace App\PartnersModule\Forms;

use \App\Forms\BaseForm,
    \Nette\DateTime,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException,
    \Vodacek\Forms\Controls\DateInput,
    \App\Model\Misc\Enum\MotivationEntryType;

/**
 * Form for creating and updating partners
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class PartnerForm extends BaseForm {


    /** @var array of users seasons */
    private $users;

    public function getUsers() {
	return $this->users;
    }

    public function setUsers($users) {
	$this->users = $users;
    }

    public function initialize() {
	
	$this->addHidden('id');

	$this->addText("name", "partnersModule.partnerForm.name")
		->addRule(Form::FILLED, "partnersModule.partnerForm.nameMustFill")
		->setRequired("partnersModule.partnerForm.nameMustFill");

	$this->addTextArea("link", "partnersModule.partnerForm.link")
		->addRule(Form::FILLED, "partnersModule.partnerForm.linkMustFill")
		->setRequired("partnersModule.partnerForm.linkMustFill");

	$this->addTextArea("note", "partnersModule.partnerForm.note", 55, 10);

	$upl = $this->addUpload("picture", "partnersModule.partnerForm.picture");
	if ($this->isCreate()) {
	    $upl->addRule(Form::FILLED, "partnersModule.partnerForm.pictureMustFill")
		    ->setRequired("partnersModule.partnerForm.pictureMustFill");
	}

	$this->addCheckbox("active", "partnersModule.partnerForm.active");
	$this->addSelect("referrer", "partnersModule.partnerForm.member", $this->getUsers());

	if ($this->isUpdate()) {
	    $this->addDate("updated", "partnersModule.partnerForm.updated", DateInput::TYPE_DATETIME_LOCAL)
		    ->setDisabled(true);
	    $this->addSelect("editor", "partnersModule.partnerForm.editor", $this->getUsers())
		    ->setDisabled(true);
	}

	$this->addSubmit("submitButton", "system.forms.submitButton.label");
	$this->onSuccess[] = callback($this->presenter, "partnerFormSuccessHandle");
    }
}
