<?php

namespace App\UsersModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Model\Misc\Exceptions;
/**
 * Form for creating and updating Users
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class UserForm extends BaseForm {

    /** @var available roles */
//    private $roles;

    /* @var available categories */
//    private $categories;
//
//    public function setRoles(array $r) {
//	if (sizeof($r) == 0)
//	    throw new \Nette\InvalidStateException('There are none roles for select');
//	$this->roles = $r;
//    }
//
//    protected function getRoles() {
//	return $this->roles;
//    }
//
//    protected function getCategories() {
//	return $this->categories;
//    }
//
//    public function setCategories(array $cats) {
//	if (sizeof($cats) == 0)
//	    throw new \Nette\InvalidStateException('There are none categories for select');
//	$this->categories = $cats;
//    }

    public function initialize() {

	$phoneNoLength = 12;
	$emailLength = 32;
	$nameLength = 32;
	$surnameLength = 32;

	$this->addHidden('id');
	//$this->addHidden('created');
	//$this->addHidden('lastLogin');
	$this->addHidden('profileStatus');
	$this->addHidden("password");

	$this->addSubmit('submitButton', "system.forms.submitButton.label");
	if ($this->getMode() == FormMode::CREATE_MODE)
	    $this->addGroup('usersModule.usrForm.newUser');
	else
	    $this->addGroup('usersModule.usrForm.updateUser');

	$this->addText('nick', 'usersModule.usrForm.nick', 16, 16)
		->addRule(Form::FILLED, 'usersModule.surnameMustFill')
		->setRequired(TRUE);
	
	if ($this->getMode() == FormMode::UPDATE_MODE)
	    $this->addCheckbox('active', 'usersModule.usrForm.active');


	$this->addText('name', 'usersModule.usrForm.name', $nameLength, $nameLength)
		->addRule(Form::FILLED, 'usersModule.usrForm.nameMustFill')
		->setRequired('usersModule.usrForm.nameMustFill');

	$this->addText('surname', 'usersModule.usrForm.surname', $surnameLength, $surnameLength)
		->addRule(Form::FILLED, 'usersModule.usrForm.surnameMustFill')
		->setRequired('usersModule.usrForm.surnameMustFill');

	$this->addText('birthNumber', 'usersModule.usrForm.birthNumber', 10, 10)
		->addRule(Form::FILLED, 'usersModule.usrForm.birthNumberMustFill')
		->addRule(Form::NUMERIC, 'usersModule.usrForm.birthNumberNumsOnly')
		->addRule(Form::LENGTH, 'usersModule.usrForm.birthNumber10CharsOnly', 10)
		->setRequired('usersModule.usrForm.birthNumberMustFill');


	$this->addText('leagueId', 'usersModule.usrForm.leagueId');

//	$this->addMultiSelect('roles', 'Role', $this->getRoles(), 6)
//		->addRule(Form::FILLED, 'Role musí být vybrána')
//		->setRequired(TRUE);
//	$this->addMultiSelect('categories', 'Kategorie', $this->getCategories(), 6)
//		->addRule(Form::FILLED, 'Kategorie musí být vybrána')
//		->setRequired(TRUE);

	$this->addGroup('usersModule.usrForm.livingAddress');
	$this->addText('street', 'usersModule.usrForm.street')
		->addRule(Form::FILLED, 'usersModule.usrForm.streetMustFill')
		->setRequired('usersModule.usrForm.streetMustFill');

	$this->addText('number', 'usersModule.usrForm.houseNum')
		->addRule(Form::FILLED, 'usersModule.usrForm.houseNumMustFill')
		->setRequired('usersModule.usrForm.houseNumMustFill');

	$this->addText('city', 'usersModule.usrForm.city')
		->addRule(Form::FILLED, 'usersModule.usrForm.cityMustFill')
		->setRequired('usersModule.usrForm.cityMustFill');

	$this->addText('postCode', 'usersModule.usrForm.postCode', 5, 5)
		->addRule(Form::FILLED, 'usersModule.usrForm.postCodeMustFill')
		->addRule(Form::NUMERIC, 'usersModule.usrForm.postCodeNumsOly')
		->addRule(Form::LENGTH, 'usersModule.usrForm.postCode5NumsOnly', 5)
		->setRequired('usersModule.usrForm.postCodeMustFill');

	$this->addGroup('usersModule.usrForm.usrContact');
	$this->addText('phone', 'usersModule.usrForm.phone', $phoneNoLength, $phoneNoLength)
		->addRule(Form::FILLED, 'usersModule.usrForm.phoneMustFill')
		->addRule(Form::NUMERIC, 'usersModule.usrForm.phoneNumsOnly')
		->addRule(Form::LENGTH, 'usersModule.usrForm.phoneNumsLength', $phoneNoLength)
		->setRequired('usersModule.usrForm.phoneMustFill');

	$this->addText('email', 'usersModule.usrForm.email', $emailLength, $emailLength)
		->addRule(Form::FILLED, 'usersModule.usrForm.emailMustFill')
		->addRule(Form::EMAIL, 'usersModule.usrForm.emailBadFormat')
		->setRequired('usersModule.usrForm.emailMustFill');

	$this->addText('job', 'usersModule.usrForm.job')
		->addRule(Form::FILLED, 'usersModule.usrForm.jobMustFill')
		->setRequired('usersModule.usrForm.jobMustFill');


	$this->addGroup('usersModule.usrForm.contactPerson');
	$this->addText('contPersonName', 'usersModule.usrForm.contName', $surnameLength, $surnameLength);

	$this->addText('contPersonPhone', 'usersModule.usrForm.contPhone', $phoneNoLength, $phoneNoLength)
		->addCondition(Form::FILLED)
		->addRule(Form::LENGTH, 'usersModule.usrForm.phoneNumsLength', $phoneNoLength)
		->addRule(Form::NUMERIC, 'usersModule.usrForm.phoneNumsOnly');
	$this->addText('contPersonMail', 'usersModule.usrForm.email', $emailLength, $emailLength)
		->addCondition(Form::FILLED)
		->addRule(Form::EMAIL, 'usersModule.usrForm.emailBadFormat');
	$this->onSuccess[] = callback($this, 'userFormSubmitted');
    }

    /**
     * Form success submission handler
     * @param \Nette\Application\UI\Form $form
     */
    public function userFormSubmitted(Form $form) {

	$values = $form->getValues();
	try {
	    switch ($this->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->presenter->createUser($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->presenter->updateUser($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $e) {
	    switch ($e->getCode()) {
		case Exceptions\DuplicateEntryException::EMAIL_EXISTS:
		    $form['email']->addError($this->tt("usersModule.usrForm.emailAlreadyExist",null,["email"=>$values->email]));
		    return;
		    break;
		case Exceptions\DuplicateEntryException::BIRTH_NUM_EXISTS:
		    $form['birthNumber']->addError($this->tt("usersModule.usrForm.birthNumberAlreadyExist",null,["number"=>$values->birthNumber]));
		    return;
		    break;
	    }
	}
    }

}
