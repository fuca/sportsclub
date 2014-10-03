<?php

namespace App\SystemModule\Forms;
use App\Forms\BaseForm;

/**
 * Description of LoginForm
 *
 * @author fuca
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
