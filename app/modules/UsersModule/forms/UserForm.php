<?php

namespace App\UsersModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \App\Services\Exceptions\DuplicateEntryException;

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

	$this->addSubmit('submitButton', 'Uložit');
	if ($this->getMode() == FormMode::CREATE_MODE)
	    $this->addGroup('Nový uživatel');
	else
	    $this->addGroup('Editace uživatele');

	$this->addText('nick', 'Přezdívka', 16, 16)
		->addRule(Form::FILLED, 'Není zadáno příjmení')
		->setRequired(TRUE);
	
	if ($this->getMode() == FormMode::UPDATE_MODE)
	    $this->addCheckbox('active', "Aktivní");


	$this->addText('name', 'Jméno', $nameLength, $nameLength)
		->addRule(Form::FILLED, 'Není zadáno jméno')
		->setRequired(TRUE);

	$this->addText('surname', 'Příjmení', $surnameLength, $surnameLength)
		->addRule(Form::FILLED, 'Není zadáno příjmení')
		->setRequired(TRUE);

	$this->addText('birthNumber', 'Rodné číslo', 10, 10)
		->addRule(Form::FILLED, 'Není zadáno rodné číslo')
		->addRule(Form::NUMERIC, 'Rodné číslo musí obsahovat pouze čísla')
		->addRule(Form::LENGTH, 'Rodné číslo musí být dlouhé 10 znaků', 10)
		->setRequired(TRUE);


	$this->addText('leagueId', 'Identifikátor v lize');

//	$this->addMultiSelect('roles', 'Role', $this->getRoles(), 6)
//		->addRule(Form::FILLED, 'Role musí být vybrána')
//		->setRequired(TRUE);
//	$this->addMultiSelect('categories', 'Kategorie', $this->getCategories(), 6)
//		->addRule(Form::FILLED, 'Kategorie musí být vybrána')
//		->setRequired(TRUE);

	$this->addGroup('Adresa trvalého bydliště');
	$this->addText('street', 'Ulice')
		->addRule(Form::FILLED, 'Pole "Adresa" je povinné')
		->setRequired(TRUE);

	$this->addText('number', 'Číslo popisné a orientační')
		->addRule(Form::FILLED, "Pole 'Číslo popisné a orientační' je povinné")
		->setRequired(TRUE);

	$this->addText('city', 'Město/Obec')
		->addRule(Form::FILLED, 'Pole "Město" je povinné')
		->setRequired(TRUE);

	$this->addText('postalCode', 'PSČ', 5, 5)
		->addRule(Form::FILLED, 'Pole "PSČ" je povinné')
		->addRule(Form::NUMERIC, 'PSČ musí obsahovat pouze čísla')
		->addRule(Form::LENGTH, 'PSČ musí být dlouhé 5 znaků', 5)
		->setRequired(TRUE);

	$this->addGroup('Kontakt na člena');
	$this->addText('phone', 'Telefon', $phoneNoLength, $phoneNoLength)
		->addRule(Form::FILLED, 'Není zadáno telefonní číslo')
		->addRule(Form::NUMERIC, 'Telefonní číslo musí obsahovat pouze čísla')
		->addRule(Form::LENGTH, "Telefon musí obsahovat $phoneNoLength znaků", $phoneNoLength)
		->setRequired(TRUE);

	$this->addText('email', 'E-mail', $emailLength, $emailLength)
		->addRule(Form::FILLED, 'Není zadáno příjmení')
		->addRule(Form::EMAIL, 'Špatný formát emailu')
		->setRequired(TRUE);

	$this->addText('job', 'Zaměstnání')
		->addRule(Form::FILLED, 'Není zadáné zaměstnání')
		->setRequired(TRUE);


	$this->addGroup('Kontaktní osoba');
	$this->addText('contPersonName', 'Jméno', $surnameLength, $surnameLength);

	$this->addText('contPersonPhone', 'Telefon', $phoneNoLength, $phoneNoLength)
		->addCondition(Form::FILLED)
		->addRule(Form::LENGTH, "Telefonní číslo musí mít max $phoneNoLength znaků", $phoneNoLength)
		->addRule(Form::NUMERIC, 'Telefonní číslo musí obsahovat pouze čísla');
	$this->addText('contPersonMail', 'E-mail', $emailLength, $emailLength)
		->addCondition(Form::FILLED)
		->addRule(Form::EMAIL, 'Špatný formát emailu');
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
	} catch (DuplicateEntryException $e) {
	    switch ($e->getCode()) {
		case 21:
		    $form->addError("User with specified email '$values->email' already exists");
		    break;
		case 22:
		    $form->addError("User with specified birth number '$values->birthNumber' already exists");
		    break;
	    }
	}
    }

}
