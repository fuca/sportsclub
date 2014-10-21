<?php

namespace App\UsersModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\WebProfileStatus,
    \Nette\Application\UI\Form;

/**
 * Form for updating WebProfiles
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class WebProfileForm extends BaseForm {

    public function initialize() {
	parent::initialize();
	
	$wpStates = WebProfileStatus::getOptions();
	$rows = 2;
	$cols = 50;
	$this->addHidden('id');
	
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
	$this->addSelect("status", "Stav", $wpStates);
	
	$this->onSuccess[] = callback($this->presenter, 'webProfileFormSuccess');
    }
}