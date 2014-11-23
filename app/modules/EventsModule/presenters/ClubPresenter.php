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

namespace App\EventsModule\Presenters;

use \App\SystemModule\Presenters\SystemClubPresenter,
    \EventCalendar\Simple\SimpleCalendar AS SC,
    \App\EventsModule\Model\Service\IEventService,
    \App\Model\Misc\Enum\EventType,
    \App\EventsModule\Forms\EventParticipationForm,
    \App\Model\Entities\EventParticipation,
    \App\Model\Misc\Enum\EventParticipationType,
    \App\Model\Entities\EventComment,
    \Nette\Utils\ArrayHash,
    \Nette\Utils\DateTime,
    \Grido\Grid,
    \App\Model\Misc\Enum\FormMode;

/**
 * Protected Event Presenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ClubPresenter extends SystemClubPresenter {
    
    /**
     * @inject
     * @var \App\EventsModule\Model\Service\IEventService
     */
    public $eventService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;
    
    protected function createTemplate($class = NULL) {
	$template = parent::createTemplate($class);
	$template->registerHelper('eventType', function ($key) {
	    return $this->tt(EventType::getOptions()[$key]);
	});
	return $template;
    }
    
    
    public function actionDefault($abbr = self::ROOT_GROUP) {	
	$data = null;
	try {
	    if (is_string($abbr))
		$sg = $this->sportGroupService->getSportGroupAbbr($abbr);
	    elseif (is_numeric($abbr))
		$sg = $this->sportGroupService->getSportGroup($abbr);
	    
	    $data = $this->eventService->getEvents($sg);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($abbr, "default", $ex);
	}
	$this->template->data = $data;
	$this->template->commentable = true;
    }
    
    public function actionShowEvent($id) {
	$event = null;
	try {
	    if (is_numeric($id)) {
		$event = $this->eventService->getEvent($id);
	    } else {
		$event = $this->eventService->getEventAlias($id);
	    }
	    $this->setEntity($event);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	$parties = $event->getParticipations();
	$uid = $this->getUser()->getIdentity()->getId();
	$this->template->data = $event;
	$this->template->commentable = true;
	$bool = $parties->filter(
		function ($e) use ($uid) {
		    if ($e->getOwner()->getId() == $uid) 
			return true;
		})->isEmpty();
	$this->template->partyExist = $bool; 
    }
    
    public function addComment(ArrayHash $values) {
	try {
	    $comment = new EventComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setAuthor($comment->getEditor());
	    $comment->setCreated(new DateTime());
	    $comment->setUpdated(new DateTime());
	    $this->eventService->createComment($comment, $this->getEntity());	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
	if (!$this->isAjax()) {
	    $this->redirect("this");
	} else {
	    $this->redrawControl("commentsData");
	}
    }
    
    public function updateComment(ArrayHash $values) {
	try {
	    $comment = new EventComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setUpdated(new DateTime());
	    $this->eventService->updateComment($comment, $this->getEntity());	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
	
	if (!$this->isAjax()) {
	    $this->redirect("this");
	} else {
	    $this->redrawControl("commentsData");
	}
    }
    
    public function deleteComment(EventComment $comm) {
	try {
	    $this->eventService->deleteComment($comm, $this->getEntity());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($comm->getId(), "this", $ex);
	}
	if ($this->isAjax()) {
	    $this->redrawControl("commentsData");
	} else {
	    $this->redirect("this");
	}
    }

    public function createComponentCalendar($name) {
	
	$cal = new SC($this, $name);
	$cal->setLanguage($this->getLocale());
	$cal->setTranslator($this->getTranslator());
	$cal->setFirstDay(SC::FIRST_MONDAY);
	$cal->setOptions([SC::OPT_SHOW_BOTTOM_NAV=>false, SC::OPT_WDAY_MAX_LEN => 2]);
	$cal->setEvents($this->eventService);
	$cal->setModifier(["abbr"=>$this->getParameter("abbr")]);
	return $cal;
    }
    
    public function actionShowEventDay($year, $month, $day) {
	try {
	    $data = $this->eventService->getForDate($year, $month, $day);
	    $date = new DateTime();
	    $date->setDate($year, $month, $day);
	    $this->template->date = $date->format(self::DATE_FORMAT);
	    $this->template->data = $data;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "this", $ex);
	}
    }
    
    // <editor-fold desc="EVENT PARTICIPATIONS">
    
    public function createComponentAcceptingParticipationForm($name) {
	$form  = new EventParticipationForm($this, $name, $this->getTranslator());
	$form->setType(EventParticipationType::YES);
	$form->initialize();
	return $form;
    }
    
    public function createComponentDenyingParticipationForm($name) {
	$form  = new EventParticipationForm($this, $name, $this->getTranslator());
	$form->setType(EventParticipationType::NO);
	$form->initialize();
	return $form;
    }
    
    public function participationFormSuccess(EventParticipationForm $form) {
	$values = $form->getValues();
	switch($form->getMode()) {
	    case FormMode::CREATE_MODE:
		$this->createEventParticipation($values);
		break;
	}
	$this->redirect("this");
    }
    
    public function createEventParticipation($values) {
	try {
	    $p = new EventParticipation((array) $values);
	    $p->setEvent($this->getEntity());
	    $p->setOwner($this->getUser()->getIdentity());
	    $this->eventService->createEventParticipation($p);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
    } 
    
    public function handleCancelParticipation() {
	try {
	    $this->eventService
		    ->deleteEventParticipation(
			    $this->getUser()->getIdentity(), 
			    $this->getEntity());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($this->getEntity()->getId(), "this", $ex);
	}
	$this->redirect("this");
    }
    
    public function createComponentParticipationControl($name) {
	$c = new \App\EventsModule\Components\ParticipationControl($this, $name);
	$c->setEvent($this->getEntity());
	$c->setEventService($this->eventService);
	return $c;
    }
    
    // </editor-fold>
}
