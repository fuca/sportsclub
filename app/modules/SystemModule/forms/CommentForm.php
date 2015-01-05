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
    \Nette\Application\UI\Form;
	
/**
 * Description of CommentForm
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 */
class CommentForm extends BaseForm {
    
    /**
     * @var array of users for selectList
     */
    private $users;
    
    public function setUsers($users) {
	$this->users = $users;
    }

    public function getUsers() {
	return $this->users;
    }
    
    public function initialize() {
	
	$this->addHidden("id");
	$this->addHidden("author");
	
	$this->addText("title", "systemModule.commentControl.title", 42)
		->setAttribute('class', 'form-control title');
	
	$this->addTextArea("content", "systemModule.commentControl.content", 45, 5)
		->setAttribute('class', 'form-control comment mceEditorComment')
		->addRule(Form::FILLED, "systemModule.commentControl.contentMustFill")
		->setRequired("systemModule.commentControl.contentMustFill");
	
//	if ($this->isUpdate()) {
//	    $this->addSelect("author", "Autor", $this->getUsers());
//	    $this->addSelect("editor", "Editor", $this->getUsers());
//	}
	
	$this->addSubmit("submit", "system.forms.submitButton.label");
//	if ($this->getShowCancel()) {
//	    $this->addSubmit("cancelButton", "system.forms.cancelButton.label")
//		    ->onClick[] = callback($this->parent, "cancelForm");
//	}
	
	$this->onSuccess[] = callback($this->parent, "commentFormSuccess");
    }
}
