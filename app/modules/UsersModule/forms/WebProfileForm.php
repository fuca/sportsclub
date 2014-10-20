<?php

namespace App\UsersModule\Forms;

use \App\Forms\BaseForm,
    \App\Model\Misc\Enum\FormMode,
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
	
	$rows = 2;
	$cols = 50;
	$this->addHidden('id');
	
	$this->addSubmit('submitButton', 'Uložit');
	
	$this->addGroup('usersModule.admin.profileEdit');

	$this->addTextArea('personalLikes', 'usersModule.webProfForm.personalLikes.label', $cols, $rows)
		->setOption("description", "usersModule.webProfForm.personalLikes.desc");

	$this->addTextArea('personalDisLikes', 'Nemám rád', $cols, $rows);

	$this->addTextArea('personalInterest', 'Zájmy', $cols, $rows);
	
	$this->addTextArea('jerseyNumber', 'Číslo dresu', $cols, $rows);
	
	$this->addTextArea('equipment', 'Vybavení', $cols, $rows);
	
	$this->addTextArea('favouriteBrand', 'Oblíbená značka', $cols, $rows);
	
	$this->addTextArea('favouriteClub', 'Oblíbený klub', $cols, $rows);
	
	$this->addTextArea('sportExperience', 'Sportovní zkušenosti', $cols, $rows);
	
	$this->addTextArea('howGotThere', 'Jak jsem se sem dostal', $cols, $rows);
	
	$this->addTextArea('aditionalInfo', 'Více o mně', $cols, $rows);
	
	$this->addTextArea('signature', 'Podpis', $cols, $rows);
	
	$this->onSuccess[] = callback($this, 'webProfileFormSuccess');
    }
}