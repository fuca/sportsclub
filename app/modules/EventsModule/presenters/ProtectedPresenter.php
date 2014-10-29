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

use \App\SystemModule\Presenters\SecuredPresenter,
    \EventCalendar\Simple\SimpleCalendar AS SC,
    \App\EventsModule\Model\Service\IEventService,
    \App\Model\Entities\EventComment,
    \Nette\Utils\ArrayHash,
    \Nette\Utils\DateTime;

/**
 * Protected Event Presenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ProtectedPresenter extends SecuredPresenter {
    
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
	$this->template->data = $event;
	$this->template->commentable = true;
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
	return $cal;
    }
}
