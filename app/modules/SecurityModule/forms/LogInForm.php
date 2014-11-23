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

namespace App\SecurityModule\Forms;

use \App\Forms\BaseForm;

/**
 * Description of LoginForm
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 */
class LogInForm extends BaseForm {
    
    public function initialize() {
	$this->addGroup("securityModule.loginControl.loginForm.heading");
	
	$userName = $this->addText('username', 'securityModule.loginControl.loginForm.email')
		->setRequired("securityModule.loginControl.loginForm.plsPutEmail");

	$passWord = $this->addPassword('password', 'securityModule.loginControl.loginForm.password')
		->setRequired('securityModule.loginControl.loginForm.plsPutPassword');

	$remember = $this->addCheckbox('remember', 'securityModule.loginControl.loginForm.remember');

	$submit = $this->addSubmit('send', 'securityModule.loginControl.loginForm.submit');
	
	/* BOOTSTRAP CSS */
	$this->getElementPrototype()->class = "form-signin";
	
	$userName->getControlPrototype()->class = "form-control";
	$userName->getLabelPrototype()->class ="sr-only";
	$userName->getControlPrototype()->type = "email";
	$userName->getControlPrototype()->addAttributes(["placeholder"=>"securityModule.loginControl.loginForm.emailPlaceholder","autofocus"=>true]);
	
	$passWord->getControlPrototype()->class = "form-control";
	$passWord->getLabelPrototype()->class ="sr-only";
	$passWord->getControlPrototype()->addAttributes(["placeholder"=>"securityModule.loginControl.loginForm.passwordPlaceholder"]);
	$remember->getLabelPrototype()->class = "checkbox";
	$submit->getControlPrototype()->class = 'btn-primary';

	
	// TODO hodit to do BaseFormu
	$this->onSuccess[] = ($h = $this->getSuccessHandler() == null)? callback($this->parent, "loginFormSuccessHandle"): $h;
    }
}
