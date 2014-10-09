<?php
/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik@gmail.com>
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

namespace App\SystemModule\Forms;

use \App\Forms\BaseForm;

/**
 * Description of LoginForm
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 */
class LogInForm extends BaseForm {
    
    public function initialize() {
	$this->addText('username', 'systemModule.loginControl.loginForm.email')
		->setRequired('Prosím zadejte email');

	$this->addPassword('password', 'systemModule.loginControl.loginForm.password')
		->setRequired('Prosím zadejte heslo.');

	$this->addCheckbox('remember', 'systemModule.loginControl.loginForm.remember');

	$this->addSubmit('send', 'systemModule.loginControl.loginForm.submit');
	
	// TODO hodit to do BaseFormu
	$this->onSuccess[] = ($h = $this->getSuccessHandler() == null)? callback($this->parent, "loginFormSuccessHandle"): $h;
    }
}
