<?php

/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik(at)gmail.com>.
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

namespace App\SecurityModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Model\Misc\Exceptions\DuplicateEntryException;

/**
 * Form for creating and updating AclRules
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class AclRuleForm extends BaseForm {

    const SLCT_MODE = "securityModule.aclForm.modeSelect",
	    SLCT_ROLE = "securityModule.aclForm.roleSelect",
	    SLCT_RESOURCE = "securityModule.aclForm.resourceSelect",
	    SLCT_ACTION = "securityModule.aclForm.actionSelect";

    /** @var array of Roles */
    private $roles;

    /** @var array of Roles */
    private $resources;

    /** @var array of Roles */
    private $privileges;

    /** @var array of Roles */
    private $modes;

    public function getResources() {
	return $this->resources;
    }

    public function getPrivileges() {
	return $this->privileges;
    }

    public function getModes() {
	return $this->modes;
    }

    public function setResources($resources) {
	$this->resources = $resources;
    }

    public function setPrivileges($privileges) {
	$this->privileges = $privileges;
    }

    public function setModes($modes) {
	$this->modes = $modes;
    }

    public function setRoles(array $roles) {
	$this->roles = $roles;
    }

    public function getRoles() {
	return $this->roles;
    }

    public function initialize() {
	$this->addHidden('id');

	if ($this->getMode() == FormMode::CREATE_MODE)
	    $this->addGroup('securityModule.aclForm.ruleNewGroup');
	else
	    $this->addGroup('securityModule.aclForm.ruleEditGroup');



	$this->addSelect('resource', 'securityModule.aclForm.resource', $this->getResources())
		->setPrompt(self::SLCT_RESOURCE);

	$this->addSelect('privilege', 'securityModule.aclForm.action', $this->getPrivileges())
		->setPrompt(self::SLCT_ACTION);

	$this->addSelect('role', 'securityModule.aclForm.role', $this->getRoles())
		->setPrompt(self::SLCT_ROLE)
		->addRule(Form::FILLED, 'securityModule.aclForm.roleMustFill')
		->setRequired('securityModule.aclForm.roleMustFill');

	$this->addSelect('mode', 'securityModule.aclForm.mode', $this->getModes())
		->setPrompt(self::SLCT_MODE)
		->addRule(Form::FILLED, 'securityModule.aclForm.modeMustFill')
		->setRequired('securityModule.aclForm.modeMustFill');

//	if($this->presenter->isAjax()) {
//	    $privSelect->addOnSubmitCallback(callback($this, "invalidateControl"), "privilegesSnippet");
//	}

	$this->addSubmit('submitButton', 'system.forms.submitButton.label');

	$this->onSubmit[] = $this->ruleFormSuccess;
    }

    public function ruleFormSuccess(Form $form) {
	$privilege = $form->getHttpData($form::DATA_TEXT, 'privilege');
	$role = $form->getHttpData($form::DATA_TEXT, 'role');
	$mode = $form->getHttpData($form::DATA_TEXT, 'mode');
	$resource = $form->getHttpData($form::DATA_TEXT, 'resource');
	
	$error = false;
	if (empty($role)) {
	    $form['role']->addError("This field is required");
	    $error = true;
	}
	if (!empty($resource)) {
	
	    if ($privilege === null) {
		$form['privilege']->addError("This field is required");
		$error = true;
	    }
	}
	
	if (empty($mode)) {
	    $form['mode']->addError("This field is required");
	    $error = true;
	}
	if ($error)
	    $this->presenter->redrawControl("privilegesSnippet");
	$values = $form->getValues();
	$values->privilege = $privilege;
	try {
	    switch ($this->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->presenter->createRule($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->presenter->updateRule($values);
		    break;
	    }
	} catch (DuplicateEntryException $e) {
	    $roleName = $this->getRoles();
	    $this->addError(
		    $this->tt("securityModule.aclForm.messages.ruleRoleResExists", null, ["role" => $roleName[$values->role], "resource" => $values->resource]));
	}
    }
}
