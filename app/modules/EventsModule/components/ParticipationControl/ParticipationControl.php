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

namespace App\EventsModule\Components;

use \Nette\Application\UI\Control,
    \Nette\ComponentModel\IContainer,
    \App\Model\Entities\Event,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Misc\Exceptions,
    \App\Model\Entities\EventParticipation,
    \App\Model\Misc\Enum\EventParticipationType AS EPT,
    \App\EventsModule\Model\Service\EventService,
    \App\EventsModule\Forms\EventParticipationForm,
    \App\UsersModule\Model\Service\IUserService;

/**
 * Description of ParticipationControl
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class ParticipationControl extends Control {
    
    private $templateMain;
    
    private $templatesDir;
    
    /**
     * @var \App\Model\Entities\Event
     */
    private $event;
    
    /**
     * @var \App\EventsModule\Model\Service\EventService
     */
    private $eventService;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    function getUserService() {
	return $this->userService;
    }

    function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }

    public function getEventService() {
	return $this->eventService;
    }

    public function setEventService(EventService $partyService) {
	$this->eventService = $partyService;
    }
    
    /**
     * @return \App\Model\Entities\Event
     * @throws Exceptions\InvalidStateException
     */
    public function getEvent() {
	if (!isset($this->event))
	    throw new Exceptions\InvalidStateException("Property Event is not correctly set, please use appropriate setter");
	return $this->event;
    }
    
    public function setEvent(Event $e) {
	$this->event = $e;
    }
    
    public function getTemplateMain() {
	return $this->templateMain;
    }

    public function setTemplateMain($template) {
	if (!file_exists($this->templatesDir . $template))
	    throw new \Nette\FileNotFoundException("Template file with specified name does not exist");
	$this->templateMain = $template;
    }
        
    public function __construct(IContainer $parent, $name) {
	parent::__construct($parent, $name);
	$this->templatesDir = __DIR__ . "/templates/";
	$this->templateMain = $this->templatesDir . "defaultMain.latte";
    }
    
    public function render($admin = false) {
	$this->template->setFile($this->getTemplateMain());
	$coll = $this->getEvent()->getParticipations();
	$parties = $coll->partition(
		function ($key, EventParticipation $e) {
		    if ($e->getType() == EPT::YES) 
			return true;
		    if ($e->getType() == EPT::NO) 
			return false;
		});
	$this->template->accepting = $parties[0];
	$this->template->denying = $parties[1];
	$this->template->admin = $admin;
	$this->template->render();
    }
    
    public function renderAdmin() {
	$this->render(true);	
    }
    
    public function handleDeleteParticipation($user) {
	try {
	    $this->getEventService()->deleteEventParticipation($user, $this->getEvent());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->presenter->flashMessage(
		    $this->presenter->tt("eventsModule.partForm.errorSave",null,["user"=>$user, "event"=>$this->getEvent()->getId()]), 
		    \App\SystemModule\Presenters\BasePresenter::FM_ERROR);	    
	}
	$this->presenter->redirect("this");
    }
    
    public function createComponentUpdateParticipationForm($name) {
	$c = new EventParticipationForm($this, $name, $this->presenter->getTranslator());
	$c->setMode(FormMode::UPDATE_MODE);
	try {
	    $users = $this->userService->getSelectUsers();
	    $c->setUsers($users);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->presenter->handleDataLoad(null, "default", $ex);
	}
	$c->initialize();
	return $c;
    }
    
    public function participationFormSuccess(EventParticipationForm $form) {
	$values = $form->getValues();
	switch($form->getMode()) {
	    case FormMode::UPDATE_MODE:

		foreach ($values->owners as $owner) {
		    $ep = new EventParticipation();
		    $ep->setOwner($owner);
		    $ep->setType($values->type);
		    $ep->setEvent($this->getEvent());
		    $ep->setContent($values->content);
		    try {
			$this->eventService->createEventParticipation($ep);
		    } catch (Exceptions\DuplicateEntryException $ex) {
			$this->presenter->flashMessage(
			    $this->presenter->tt("eventsModule.partForm.errorDupl", null,["user"=>$owner, "event"=>$this->getEvent()->getId()]), 
			    \App\SystemModule\Presenters\BasePresenter::FM_ERROR);
		    } catch (Exceptions\DataErrorException $ex) {
			$this->presenter->flashMessage(
			    $this->presenter->tt("eventsModule.partForm.errorSave", null,["user"=>$owner, "event"=>$this->getEvent()->getId()]), 
			    \App\SystemModule\Presenters\BasePresenter::FM_ERROR);
		    }
		}

		break;
	}
	if (!$this->presenter->isAjax()) {
	    $this->presenter->redirect("this");
	} else {
	    // invalidate control
	}
    }

}
