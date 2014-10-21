<?php

namespace App\UsersModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Model\Misc\Exceptions,  
    \Nette\Security\Passwords;

/**
 * Form for changing password
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class PasswordChangeForm extends BaseForm {

    public function initialize() {

	$this->addGroup("usersModule.passwordChangeForm.group");
	$this->addPassword("old", "usersModule.passwordChangeForm.oldPw")
		->addRule(Form::FILLED, "usersModule.passwordChangeForm.oldPwHasToBeFilled")
		->setRequired("usersModule.passwordChangeForm.oldPwHasToBeFilled");
	
	$this->addPassword("new1", "usersModule.passwordChangeForm.new1")
		->addRule(Form::FILLED, "usersModule.passwordChangeForm.newPwHasToBeFilled")
		->setRequired("usersModule.passwordChangeForm.newPwHasToBeFilled");
	
	$this->addPassword("new2", "usersModule.passwordChangeForm.new2")
		->addRule(Form::FILLED, "usersModule.passwordChangeForm.newPwHasToBeFilled")
		->addCondition(Form::FILLED, $this['new1'])
		->addRule(Form::EQUAL, "usersModule.passwordChangeForm.newPwsDontMatch", $this['new1']->value)
		->setRequired("usersModule.passwordChangeForm.newPwHasToBeFilled");
	
	$this->addSubmit("submitButton", "system.forms.submitButton.label");
	$this->onSuccess[] = callback($this->presenter, 'passwordChangeFormSuccess');
    }
}
