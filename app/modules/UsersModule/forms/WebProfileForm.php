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
	
	$this->addGroup('usersModule.admin.profileEdit');

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
	$this->addSelect("status", "usersModule.webProfForm.status.label", $wpStates);
	
	$this->addSubmit('submitButton', 'system.forms.submitButton.label');
	
	$this->onSuccess[] = callback($this->presenter, 'webProfileFormSuccess');
    }
}