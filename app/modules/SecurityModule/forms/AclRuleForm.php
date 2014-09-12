<?php

namespace App\SecurityModule\Forms;

use App\Forms\BaseForm,
    App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException;

/**
 * Form for creating and updating AclRules
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class AclRuleForm extends BaseForm {
    
    const SLCT_MODE = "Vyberte mód..",
	  SLCT_ROLE = "Vyberte roli..",
	  SLCT_RESOURCE = "Vyberte zdroj..",
	  SLCT_ACTION = "Vyberte akci..";
    
    
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
	    $this->addGroup('Nové pravidlo');
	else
	    $this->addGroup('Editace pravidla');

	$this->addSelect('role', 'Role', $this->getRoles())
		->setPrompt(self::SLCT_ROLE)
		->addRule(Form::FILLED, self::SLCT_ROLE)
		->setRequired(true);

	$this->addSelect('resource', 'Zdroj', $this->getResources())
		->setPrompt(self::SLCT_RESOURCE)
		->addRule(Form::FILLED, self::SLCT_RESOURCE)
		->setRequired(true);

	$this->addSelect('privilege', 'Akce', $this->getPrivileges())
		->setPrompt(self::SLCT_ACTION)
		->addRule(Form::FILLED, self::SLCT_ACTION)
		->setRequired(true);

	
	$this->addSelect('mode', 'Mód', $this->getModes())
		->setPrompt(self::SLCT_MODE)
		->addRule(Form::FILLED, self::SLCT_MODE)
		->setRequired(true);
	
	$this->addSubmit('submitButton', 'Uložit');

	$this->onSuccess[] = callback($this, 'ruleFormSubmitted');
    }

    /**
     * Form success submission handler
     * @param \Nette\Application\UI\Form $form
     */
    public function ruleFormSubmitted(Form $form) {

	$values = $form->getValues();
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
	    $this->addError("Rule with pair '{$roleName[$values->role]}, {$values->resource}' already exists");
	}
    }

}
