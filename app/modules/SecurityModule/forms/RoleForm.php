<?php

namespace App\SecurityModule\Forms;

use App\Forms\BaseForm,
    App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException;

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
	    $this->addGroup('Nová role');
	else
	    $this->addGroup('Editace role');

	$this->addText('name', 'Název')
		->addRule(Form::FILLED, "Pole Název je povinné")
		->setRequired(TRUE);

	$this->addCheckboxList('parents', 'Předci', $this->roles);

	$this->addTextArea('note', 'Poznámka');

	$this->addSubmit('submitButton', 'Uložit');

	$this->onSuccess[] = callback($this->presenter, 'roleFormSubmitted');
    }
}
