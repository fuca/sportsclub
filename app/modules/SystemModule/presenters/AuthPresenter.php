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
    \App\SystemModule\Presenters\BasePresenter,
    App\SystemModule\Forms\LogInForm,
    Kdyby\Monolog\Logger;

/**
 * AuthPresenter
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AuthPresenter extends BasePresenter {
    
    /**
     * @inject 
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $users;
    
    public function actionDefault() {
	$this->logger->addInfo("Jsme v AUTH IN");
	$this->redirect("in");
    }
    
    public function actionIn() {
//	// set pw admin to user no 1
//	$u = $this->users->getUser(1);
//	//dd($u);
//	$o = ['salt'=>$this->context->parameters['models']['salt'], 'cost'=>4];
//	//dd("salt", $o);
//	$h = \App\Misc\Passwords::hash("admin", $o);
//	//dd($h);    
//	$this->users->updateUser($u->setPassword($h));
////	dd(\App\Misc\Passwords::verify("admin", $h));
//	// $2y$04$$2a06$05IKqFG8iuPts/ceDww1QeqjiwOTM3OaQI8W.VyN3/1Ur.i
    }

    public function actionOut() {
	$this->getUser()->logout();
	$this->flashMessage('Byl jste odhlášen.');
	$this->redirect('in');
    }

}
