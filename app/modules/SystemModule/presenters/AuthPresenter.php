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

namespace App\SystemModule\Presenters;

use Nette\Application\UI\Form,
    Nette\Security\AuthenticationException,
    \App\SystemModule\Presenters\BasePresenter;

/**
 * AuthPresenter
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AuthPresenter extends BasePresenter {

    /**
     * Sign-in form factory.
     * @return Nette\Application\UI\Form
     */
    protected function createComponentSignInForm() {
	$form = new Form;
	$form->addText('username', 'Username:')
		->setRequired('Please enter your username.');

	$form->addPassword('password', 'Password:')
		->setRequired('Please enter your password.');

	$form->addCheckbox('remember', 'Keep me signed in');

	$form->addSubmit('send', 'Sign in');

	$form->onSuccess[] = $this->signInFormSucceeded;
	return $form;
    }

    public function signInFormSucceeded($form) {
	$values = $form->getValues();

	if ($values->remember) {
	    // TODO move time values into DB?
	    $this->getUser()->setExpiration('14 days', FALSE);
	} else {
	    $this->getUser()->setExpiration('20 minutes', TRUE);
	}

	try {
	    $this->getUser()->login($values->username, $values->password);
	    $this->redirect(':Public:Homepage:default');
	} catch (AuthenticationException $e) {
	    $form->addError($e->getMessage());
	}
    }

    public function actionOut() {
	$this->getUser()->logout();
	$this->flashMessage('You have been signed out.');
	$this->redirect('in');
    }

}
