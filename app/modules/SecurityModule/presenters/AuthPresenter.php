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

namespace App\SecurityModule\Presenters;

use \Nette\Application\UI\Form,
    \Nette\Security\AuthenticationException,
    \App\SystemModule\Presenters\BasePresenter,
    \App\SystemModule\Forms\LogInForm,
    \Kdyby\Monolog\Logger;

/**
 * Authorization presenter
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AuthPresenter extends BasePresenter {
    
    /**
     * @inject 
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $users;
    
    public function actionDefault() {
	$this->redirect("in");
    }
    
    public function actionIn() {
    }

    public function actionOut() {
	$this->getUser()->logout(TRUE);
	$this->flashMessage($this->tt("securityModule.loginControl.messages.uWereLoggedOut"), self::FM_INFO);
	$this->redirect('in');
    }

}
