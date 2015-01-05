<?php

namespace App\SystemModule\Components;

use \Nette\Application\UI\Control,
    \App\Model\Misc\Exceptions,
    \App\Model\Entities\User,
    \App\UsersModule\Model\Service\IUsersService;
/**
 * Component for display club contact information on homepage and within contacts page
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class ContactControl extends Control {
    
    /**
     * @var string directory with templates
     */
    private $templatesDir;

    /** 
     * @var string template file 
     */
    private $templateFile;
    
    /** 
     * @var string homepage template file 
     */
    private $homepageTemplateFile;

    
    private $userEntity;
    
    public function __construct($parent, $name) {
	parent::__construct($parent, $name);
	$this->templatesDir	    = __DIR__ . "/templates/";
	$this->templateFile	    = $this->templatesDir . "default.latte";
	$this->homepageTemplateFile = $this->templatesDir . "homepage.latte";
	
	
    }

    public function getTemplateFile() {
	return $this->templateFile;
    }

    public function setTemplateFile($template) {
	if (!file_exists($this->templatesDir . $template))
	    throw new \Nette\FileNotFoundException("Template file with name '$template' does not exist");
	$this->templateFile = $template;
    }
    
    public function setHomepageTemplateFile($template) {
	if (!file_exists($this->templatesDir . $template))
	    throw new \Nette\FileNotFoundException("Template file with name '$template' does not exist");
	$this->homepageTemplateFile = $template;
    }
    
    
    public function setUser(User $u) {
	$this->userEntity = $u;
    } 
    
    public function getUser() {
	if (!isset($this->userEntity)) {
	    throw new Exceptions\InvalidStateException("Please set user entity!");
	}

	return $this->userEntity;
    }
    
    /**
     * Render full contact control 
     */
    public function render() {
	$this->template->setFile($this->templateFile);
	$user = $this->getUser();
	$this->template->title = "{$user->getName()} {$user->getSurname()}";
	$contact = $user->getContact();
	$address = $contact->getAddress();
	$this->template->street = $address->getStreet();
	$this->template->number = $address->getNumber();
	$this->template->postCode = $address->getPostCode();
	$this->template->city = $address->getCity();
	$this->template->in = $address->provideIdentificationNumber();
	$this->template->tin = $address->provideTaxIdentificationNumber();
	$this->template->account = $address->provideAccountNumber();
	
	$this->template->cpPersonName = $contact->getContPersonName();
	$this->template->cpPersonPhone = $contact->getContPersonPhone();
	$this->template->cpPersonMail = $contact->getContPersonMail();
	$this->template->render();
    }
    
    /**
     * Renders quick contact for purpose of displaying on homepage
     */
    public function renderHomepage() {
	$this->template->setFile($this->homepageTemplateFile);
	$user = $this->getUser();
	$this->template->title = "{$user->getName()} {$user->getSurname()}";
	$address = $user->getContact()->getAddress();
	$this->template->street = $address->getStreet();
	$this->template->number = $address->getNumber();
	$this->template->postCode = $address->getPostCode();
	$this->template->city = $address->getCity();
	$this->template->in = $address->provideIdentificationNumber();
	$this->template->tin = $address->provideTaxIdentificationNumber();
	$this->template->account = $address->provideAccountNumber();
	$this->template->moreContactsLink = ":Security:Public:default";
	$this->template->render();
    }


}
