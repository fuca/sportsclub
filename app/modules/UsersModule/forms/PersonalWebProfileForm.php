<?php

namespace App\UsersModule\Forms;

use \App\Forms\BaseForm,
    \Nette\Application\UI\Form;

/**
 * Form for updating WebProfiles by owning user
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class PersonalWebProfileForm extends BaseForm {

    public function initialize() {
	parent::initialize();
	
	$rows = 2;
	$cols = 50;
	$this->addHidden('id');
	$this->addHidden("status");
	
	$this->addSubmit('submitButton', 'Uložit');
	
	$this->addGroup('usersModule.admin.profileEdit');

	$this->addTextArea('personalLikes', 'usersModule.webProfForm.personalLikes.label', $cols, $rows)
		->setOption("description", "usersModule.webProfForm.personalLikes.desc");

	$this->addTextArea('personalDislikes', 'Nemám rád', $cols, $rows);

	$this->addTextArea('personalInterests', 'Zájmy', $cols, $rows);
	
	$this->addText('jerseyNumber', 'Číslo dresu')
		->addRule(Form::NUMERIC, "Číslo dresu musí být číslo");
	
	$this->addTextArea('equipment', 'Vybavení', $cols, $rows);
	
	$this->addTextArea('favouriteBrand', 'Oblíbená značka', $cols, $rows);
	
	$this->addTextArea('favouriteClub', 'Oblíbený klub', $cols, $rows);
	
	$this->addTextArea('sportExperience', 'Sportovní zkušenosti', $cols, $rows);
	
	$this->addTextArea('howGotThere', 'Jak jsem se sem dostal', $cols, $rows);
	
	$this->addTextArea('aditionalInfo', 'Více o mně', $cols, $rows);
	
	$this->addTextArea('signature', 'Podpis', $cols, $rows);
	
	$this->onSuccess[] = callback($this->presenter, 'webProfileFormSuccess');
    }
}