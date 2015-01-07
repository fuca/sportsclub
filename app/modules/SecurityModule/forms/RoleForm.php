<?php

namespace App\SecurityModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Model\Misc\Exceptions\DuplicateEntryException;

/**
 * Form for creating and updating Roles
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
class RoleForm extends BaseForm {

    /** @var array of Roles */
    private $roles;

    public function setRoles(array $roles) {
	$this->roles = $roles;
    }

    public function initialize() {
	$this->addHidden('id');

	if ($this->isCreate())
	    $this->addGroup('securityModule.roleForm.newRoleGroup');
	else
	    $this->addGroup('securityModule.roleForm.editRoleGroup');

	$this->addText('name', 'securityModule.roleForm.name')
		->addRule(Form::FILLED, "securityModule.roleForm.nameRequired")
		->setRequired(TRUE);

	$this->addCheckboxList('parents', 'securityModule.roleForm.parents', $this->roles);

	$this->addTextArea('note', 'securityModule.roleForm.note');
	
	if ($this->isUpdate()) {
	    $this->addDate("added", 'securityModule.roleForm.note')
		->setRequired();
	}

	$this->addSubmit('submitButton', 'system.forms.submitButton.label');

	$this->onSuccess[] = callback($this->presenter, 'roleFormSubmitted');
    }
}
