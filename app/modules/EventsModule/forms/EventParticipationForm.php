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

namespace App\EventsModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\EventParticipationType,
    \App\Model\Misc\Exceptions;

/**
 * Form for creating event participations
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class EventParticipationForm extends BaseForm {

    /**
     * @var EventParticipationType
     */
    protected $type;
    
    /**
     * @var array of select users
     */
    protected $users;
    
    function getUsers() {
	return $this->users;
    }

    function setUsers($users) {
	$this->users = $users;
    }
    
    public function setType($type) {
	$this->type = $type;
    }
    
    public function getType() {
	if (!isset($this->type))
	    throw new Exceptions\InvalidStateException("Set up type first");
	return $this->type;
    }

    public function initialize() {
	$this->addHidden("id");
	
	if ($this->isCreate()) {
	    $this->addHidden("type");
	    $this['type']->value = $this->getType();

	    $this->addTextArea("content", "eventsModule.partForm.content", 40, 3);
	} else {
	    $this->addMultiSelect("owners", "eventsModule.partForm.owners", $this->getUsers(), 20)
		    ->setRequired("eventsModule.partForm.ownersMustSel");
	    
	    $this->addSelect("type", "eventsModule.partForm.type", EventParticipationType::getOptions())
		    ->setRequired("eventsModule.partForm.typeMustSel");
	    
	    $this->addTextArea("content", "eventsModule.partForm.content", 40, 3);
	}
	$this->addSubmit("submitButton", "system.forms.submitButton.label")
		->getControlPrototype()->class = "btn btn-primary";

	$this->onSuccess[] = callback($this->parent, "participationFormSuccess");
    }
}
