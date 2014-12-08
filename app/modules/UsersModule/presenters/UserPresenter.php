<?php

/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\UsersModule\Presenters;

use \App\SystemModule\Presenters\SystemUserPresenter,
    \App\UsersModule\Forms\PersonalWebProfileForm,
    \App\UsersModule\Model\Misc\Utils\UserEntityManageHelper,
    \App\SecurityModule\Model\Misc\Annotations\Secured,
    \App\UsersModule\Forms\PersonalUserForm,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Entities\WebProfile,
    \App\Model\Misc\Enum\WebProfileStatus,
    \App\Model\Entities\User,
    \Nette\Security\Passwords,
    \App\UsersModule\Forms\PasswordChangeForm,
    \Nette\Utils\ArrayHash,
    \App\Model\Entities\Address,
    \App\Model\Entities\Contact,
    \App\Model\Misc\Exceptions;

/**
 * Presenter for maintain User section of Users module
 * @Secured(resource="UsersUser")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class UserPresenter extends SystemUserPresenter {

    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;
    
    /**
     * @inject 
     * @var \App\SystemModule\Model\Service\INotificationService
     */
    public $notifService;  
    
    /**
     * @Secured(resource="default")
     */
    public function actionDefault() {
	
    }
    
    public function actionData() {
	$uUser = null;
	try {
	    $form = $this->getComponent("userDataForm");
	    $uUser = $this->getUser()->getIdentity();
	    $data = $uUser->toArray() + $uUser->getContact()->toArray() + $uUser->getContact()->getAddress()->toArray();
	    $form->setDefaults($data);
	} catch (Exceptions\DataErrorException $e) {
	    $this->handleDataLoad($uUser->getId(), "default", $e);
	}
    }

    public function userFormSuccess(PersonalUserForm $form) {
	$values = $form->getValues();
	try {
	    $this->userService->updateUser(UserEntityManageHelper::hydrateUserFromHash($values));
	} catch (Exceptions\DuplicateEntryException $ex) {
	   switch ($ex->getCode()) {
		case Exceptions\DuplicateEntryException::EMAIL_EXISTS:
		    $form['email']->addError("User with specified email '$values->email' already exists");
		    break;
		case Exceptions\DuplicateEntryException::BIRTH_NUM_EXISTS:
		    $form['birthNumber']->addError("User with specified birth number '$values->birthNumber' already exists");
		    break;
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "default", $ex);
	}
    }

    public function actionProfile() {
	$uWp = null;
	try {
	    $form = $this->getComponent("userWebProfileForm");
	    $uWp = $this->getUser()->getIdentity()->getWebProfile();
	    $data = $uWp->toArray();
	    $form->setDefaults($data);
	    $this->template->profile = $uWp;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($uWp->getId(), "default", $ex);
	}
    }

    /**
     * Performs WebProfile data save from owning logged user
     * @param \App\UsersModule\Forms\PersonalWebProfileForm $form
     */
    public function webProfileFormSuccess(PersonalWebProfileForm $form) {
	$values = $form->getValues();
	
	$wp = new WebProfile((array) $values);
	$user = $this->getUser()->getIdentity();
	
	$wp->setStatus(WebProfileStatus::UPDATED);
	$wp->setUpdated(new \Nette\Utils\DateTime());
	$wp->setEditor($this->getUser()->getIdentity());
	$user->setWebProfile($wp);
	
	try {
	    $this->userService->updateUser($user);
	} catch (Exceptions\DataErrorException $e) {
	    $this->handleDataSave($wp->getId(), "default", $e);
	}
	$this->redirect("this");
    }
    
    public function passWordChangeFormSuccess(PasswordChangeForm $form) {
	$values = $form->getValues();
	$user = $this->getUser()->getIdentity();
	
	if (!Passwords::verify($values->old, $user->getPassword())) {
	    $form['old']->addError("usersModule.passwordChangeForm.oldPwDoesntMatch");
	    return;
	}
	try {
	    $hash = $this->userService->generateNewPassword($values->new1);
	    $user->setPasswordChangeRequired(false);
	    $this->userService->updateUser($user->setPassword($hash));
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($user->getId(), "default", $ex);
	}
	$this->flashMessage($this->tt("usersModule.messages.passwordChanged"), self::FM_SUCCESS);
	$this->notifService->notifyPasswordChange($user->insertRawPassword($values->new1));
	$this->redirect("this");
    }

    public function createComponentUserDataForm($name) {
	$form = new PersonalUserForm($this, $name, $this->getTranslator());
	$form->initialize();
	return $form;
    }

    public function createComponentUserWebProfileForm($name) {
	$form = new PersonalWebProfileForm($this, $name, $this->getTranslator());
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    public function createComponentUserPasswordChangeForm($name) {
	$form = new PasswordChangeForm($this, $name, $this->getTranslator());
	$form->initialize();
	return $form;
    }

}
