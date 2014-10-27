<?php

namespace App\SecurityModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Model\Misc\Exceptions\DuplicateEntryException;

/**
 * Form for creating and updating Positions
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class PositionForm extends BaseForm {

    /** @var array of Roles */
    private $roles;
    
    /** @var array of Groups */
    private $sportGroups;
    
    /** @var array of Users */
    private $users;
    
    public function setSportGroups(array $groups) {
	$this->sportGroups = $groups;
    }

    public function setUsers(array $users) {
	$this->users = $users;
    }

    public function setRoles(array $roles) {
	$this->roles = $roles;
    }    
    
    public function getRoles() {
	return $this->roles;
    }

    public function getSportGroups() {
	return $this->sportGroups;
    }

    public function getUsers() {
	return $this->users;
    }

    public function initialize() {
	$this->addHidden('id');

	if ($this->getMode() == FormMode::CREATE_MODE)
	    $this->addGroup('securityModule.posForm.newPosGroup');
	else
	    $this->addGroup('securityModule.posForm.editPosGroup');

	$this->addSelect("owner", "securityModule.posForm.user", $this->getUsers())
		->setPrompt("securityModule.posForm.userSelect")
		->addRule(Form::FILLED, "securityModule.posForm.userMustSelect")
		->setRequired(true);
	
	$this->addSelect("role", "securityModule.posForm.role", $this->getRoles())
		->setPrompt("securityModule.posForm.roleSelect")
		->addRule(Form::FILLED, "securityModule.posForm.roleMustSelect")
		->setRequired(true);
	
	$this->addSelect("group", "securityModule.posForm.group", $this->getSportGroups())
		->setPrompt("securityModule.posForm.groupSelect")
		->addRule(Form::FILLED, "securityModule.posForm.groupMustSelect")
		->setRequired(true);
	
	$this->addCheckbox("publishContact", "securityModule.posForm.publishContact")
		->addCondition(Form::EQUAL, true)
		->toggle("position-name");
	
	$this->addText("name", "securityModule.posForm.name")
		->setOption("id", "position-name")
		->addCondition(Form::EQUAL, $this["publishContact"]->value, true)
		->addRule(Form::FILLED, "securityModule.posForm.nameMustFill");

	$this->addTextArea('comment', 'securityModule.posForm.note');

	$this->addSubmit('submitButton', 'system.forms.submitButton.label');

	$this->onSuccess[] = callback($this, 'positionFormSubmitted');
    }

    /**
     * Form success submission handler
     * @param \Nette\Application\UI\Form $form
     */
    public function positionFormSubmitted(Form $form) {

	$values = $form->getValues();
	try {
	    switch ($this->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->presenter->createPosition($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->presenter->updatePosition($values);
		    break;
	    }
	} catch (DuplicateEntryException $e) {
	    $this->addError("securityModule.posForm.posExist");
	}
    }

}
