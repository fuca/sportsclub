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

namespace App\SecurityModule\Components;

use \Nette\Application\UI\Control,
    \App\SecurityModule\Forms\LoginForm,
    \Nette\ComponentModel\IContainer,
    \Nette\Security\User,
    \Nette\Security\AuthenticationException,
    \Nette\Application\UI\Link;

/**
 * Description of LoginControl
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class LogInControl extends Control {

    /** @var string templates dir */
    private $templatesDir;

    /** log in link target */
    private $logInTarget;

    /** @var log out link target */
    //private $logOutTarget;

    /** @var string main template file */
    private $templateMain;

    /** @var string user dara template file */
    private $templateUser;

    /** @var string form template file */
    private $templateForm;

    /** @var Nette\Security\User */
    private $user;

    public function getUser() {
	if (!isset($this->user))
	    $this->user = $this->presenter->getUser();
	return $this->user;
    }

    public function setLogInTarget($logInTarget) {
//	if ($logInTarget === null) 
//	    throw new \App\Model\Misc\Exceptions\NullPointerException("Argument Callback was null", 0);
	$this->logInTarget = $logInTarget;
    }

//    public function setLogOutTarget($logOutTarget) {
//	$this->logOutTarget = $logOutTarget;
//    }

    public function setTemplateMain($templateMain) {
	if (!file_exists($this->templatesDir . $templateMain))
	    throw new \Nette\FileNotFoundException("Template file with specified name does not exist");
	$this->templateMain = $templateMain;
    }

    public function setTemplateUser($templateUser) {
	if (!file_exists($this->templatesDir . $templateUser))
	    throw new \Nette\FileNotFoundException("Template file with specified name does not exist");
	$this->templateUser = $templateUser;
    }

    public function setTemplateForm($templateForm) {
	if (!file_exists($this->templatesDir . $templateForm))
	    throw new \Nette\FileNotFoundException("Template file with specified name does not exist");
	$this->templateForm = $templateForm;
    }

    public function __construct(IContainer $parent = NULL, $name = NULL) {
	parent::__construct($parent, $name);
	$this->templatesDir = __DIR__ . "/templates/";
	$this->templateMain = $this->templatesDir . "defaultMain.latte";
	$this->templateUser = $this->templatesDir . "defaultUser.latte";
	$this->templateForm = $this->templatesDir . "defaultForm.latte";
    }

    public function createComponentLoginForm($name) {
	$form = new LogInForm($this, $name, $this->presenter->translator);
	$form->initialize();
	return $form;
    }

    public function loginFormSuccessHandle($form) {
	$values = $form->getValues();

	if ($values->remember) {
	    $this->presenter->getUser()->setExpiration('14 days', FALSE);
	} else {
	    $this->presenter->getUser()->setExpiration('20 minutes', TRUE);
	}
	try {
	    $this->presenter->getUser()->login($values->username, $values->password);
	    $bl = $this->presenter->getParameter("backlink");
	    if ($bl) {
		$this->presenter->redirect($this->presenter->restoreRequest($bl));
	    } else {
		$this->presenter->redirect($this->logInTarget);
	    }
	} catch (AuthenticationException $e) {
	    $form->addError($this->getPresenter()->getTranslator()->translate($e->getMessage()));
	}
    }

    public function render() {
	$loggedIn = $this->getUser()->isLoggedIn();
	$this->template->setFile($this->templateMain);
	$this->template->logInLink = $this->presenter->link(":Security:Auth:in");
	$this->template->logOutLink = $this->presenter->link(":Security:Auth:out");
	$this->template->isLoggedIn = $loggedIn;
	$this->template->user = $loggedIn ? $this->getUser()->getIdentity() : null;
	$this->template->pmsCount = 3;
	$this->template->messagesMenu = true;
	$this->template->adminMenuPredicate = true;
	$this->template->render();
    }

    public function renderForm() {
	$this->template->setFile($this->templateForm);

	$this->template->render();
    }

    public function renderUser() {
	$loggedIn = $this->getUser()->isLoggedIn();
	$this->template->setFile($this->templateUser);
	$this->template->isLoggedIn = $loggedIn;
	$this->template->user = $loggedIn ? $this->getUser()->getIdentity() : null;
	$this->template->userProfileLink = $this->presenter->link(":Users:Protected:profile");
	$this->template->render();
    }
    
    public function createComponentProtectedMenu($name) {
	return $this->presenter->createComponentProtectedMenu($name);
    }

    public function createComponentAdminMenu($name) {
	return $this->presenter->createComponentAdminMenu($name);
    }

    public function createComponentCommonMenu($name) {
	$res = $this->presenter->createComponentCommonMenu($name);
	return $res;
    }

    public function createComponentMailMenu($name) {
	$res = $this->presenter->createComponentMailMenu($name);
	return $res;
    }
}
