<?php

namespace App\SecurityModule\Forms;

use App\Forms\BaseForm,
    App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException;

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
	    $this->addGroup('Nová pozice');
	else
	    $this->addGroup('Editace pozice');

	$this->addSelect("owner", "Uživatel", $this->getUsers())
		->setPrompt("Vyberte uživatele..")
		->addRule(Form::FILLED, "Uživatel musí být vybrán")
		->setRequired(true);
	
	$this->addSelect("role", "Role", $this->getRoles())
		->setPrompt("Vyberte roli..")
		->addRule(Form::FILLED, "Role musí být vybrána")
		->setRequired(true);
	
	$this->addSelect("group", "Skupina", $this->getSportGroups())
		->setPrompt("Vyberte skupinu..")
		->addRule(Form::FILLED, "Skupina musí být vybrána")
		->setRequired(true);

	$this->addTextArea('comment', 'Poznámka');

	$this->addSubmit('submitButton', 'Uložit');

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
	    $this->addError("This of Position already exists");
	}
    }

}
