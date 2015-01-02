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
final class PersonalUserForm extends BaseForm {

    public function initialize() {

	$phoneNoLength = 12;
	$emailLength = 32;
	$nameLength = 32;
	$surnameLength = 32;

	$this->addHidden('id');
	$this->addHidden("password");
	$this->addHidden("leagueId");

	$this->addSubmit('submitButton', "system.forms.submitButton.label");
	
	$this->addGroup('Osobní údaje');

	$this->addText('nick', 'Přezdívka', 16, 16)
		->addRule(Form::FILLED, 'Není zadáno příjmení')
		->setRequired(TRUE);

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

	$this->addGroup('Kontakt');
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
	$this->onSuccess[] = callback($this->presenter, 'userFormSuccess');
    }
}
