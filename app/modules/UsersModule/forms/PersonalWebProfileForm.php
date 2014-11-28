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

	$this->addTextArea('personalLikes', 'usersModule.webProfForm.personalLikes.label', $cols, $rows);

	$this->addTextArea('personalDislikes', 'usersModule.webProfForm.personalDisLikes.label', $cols, $rows);

	$this->addTextArea('personalInterests', 'usersModule.webProfForm.personalInterests.label', $cols, $rows);
	
	$this->addText('jerseyNumber', 'usersModule.webProfForm.jerseyNumber.label')
		->addRule(Form::NUMERIC, "usersModule.webProfForm.jerseyNumber.mustNumber");
	
	$this->addTextArea('equipment', 'usersModule.webProfForm.equipment.label', $cols, $rows);
	
	$this->addTextArea('favouriteBrand', 'usersModule.webProfForm.favBrand.label', $cols, $rows);
	
	$this->addTextArea('favouriteClub', 'usersModule.webProfForm.favClub.label', $cols, $rows);
	
	$this->addTextArea('sportExperience', 'usersModule.webProfForm.experience.label', $cols, $rows);
	
	$this->addTextArea('howGotThere', 'usersModule.webProfForm.howGotThere.label', $cols, $rows);
	
	$this->addTextArea('aditionalInfo', 'usersModule.webProfForm.moreAboutMe.label', $cols, $rows);
	
	$this->addUpload("picture", "usersModule.webProfForm.picture.label");
	
	$this->addTextArea('signature', 'usersModule.webProfForm.signature.label', $cols, $rows);
	
	$this->addCheckbox("publish", 'usersModule.webProfForm.publish.label');
	
	$this->addSubmit('submitButton', 'system.forms.submitButton.label');
	
	$this->onSuccess[] = callback($this->presenter, 'webProfileFormSuccess');
    }
}