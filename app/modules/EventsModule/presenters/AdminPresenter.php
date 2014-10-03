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
    \App\EventsModule\Model\Service\EventService,
    \App\SystemModule\Model\Service\ISportGroupService,
    App\Model\Service\IUserService,
    \Nette\Utils\ArrayHash,
    \App\Model\Entities\Event,
    \App\PaymentsModule\Forms\EventForm,
    \App\Model\Misc\Enum\EventType,
    \Grido\Grid,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Misc\Exceptions,
    \App\Forms\BaseForm;

/**
 * AdminEventPresenter
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 * @Secured
 */
class AdminPresenter extends SecuredPresenter {

    /**
     * @inject
     * @var \App\EventsModule\Model\Service\IEventService
     */
    public $eventsService;

    /**
     * @inject 
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupsService;
    
    /**
     * @inject
     * @var \App\Model\Service\IUserService
     */
    public $userService;

    public function actionDefault() {
	
    }

    public function actionAddEvent() {
	// NASTAVIT AUTORA v SELECTU???
    }

    public function actionUpdateEvent($id) {
	try {
	    $e = $this->eventsService->getEvent($id);
	    if ($e !== null) {
		$form = $this->getComponent("updateEventForm");
		$grArr = $e->getGroups()->map(function($e){return $e->getId();})->toArray();
		$e->setGroups($grArr);
		$form->setDefaults($e->toArray());
	    }
	} catch (Exceptions\EntityNotFoundException $ex) {
	    $this->flashMessage("Entita s danym id neexistuje", self::FM_ERROR);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->flashMessage("Nepodarilo se nacist pozadovana data", self::FM_ERROR);
	} finally {
	    $this->logger->addError("Adm Events pres actionUpdateEvent ERR fetch event with id $id");
	    //$this->redirect("default");
	}
    }

    public function createEvent(ArrayHash $values) {
	try {
	    $e = new Event((array) $values);
	    $e->setAuthor($this->getUser()->getIdentity());
	    $this->eventsService->createEvent($e);
	} catch (Exception $ex) {
	    $this->flashMessage("Nepodařilo se provést požadovanou změnu", self::FM_ERROR);
	}
    }

    public function updateEvent(ArrayHash $values) {
	try {
	    $e = new Event((array) $values);
	    $e->setEditor($this->getUser()->getIdentity());
	    $this->eventsService->updateEvent($e);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se uložit požadované změny", self::FM_ERROR);
	}
    }

    public function handleDeleteEvent($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Bad argument format", self::FM_WARNING);
	    $this->redirect("this");
	}
	try {
	    $this->eventsService->deleteEvent($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se uložit požadované změny", self::FM_ERROR);
	}
    }

    public function eventFormSubmittedHandle(BaseForm $form) {
	$values = $form->getValues();
	try {
	    switch ($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createEvent($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateEvent($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $form->addError("eventsModule.eventForm.errors.eventAlreadyExist");
	}
    }

    public function createComponentAddEventForm($name) {
	$form = $this->prepareEventForm($name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateEventForm($name) {
	$form = $this->prepareEventForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    public function prepareEventForm($name) {
	$form = new EventForm($this, $name, $this->getTranslator());
	try {
	    $groups = $this->sportGroupsService->getSelectSportGroups();
	    $form->setSportGroups($groups);
	    $users = $this->userService->getSelectUsers();
	    $form->setUsers($users);
	} catch (Exception $ex) {
	    $this->flashMessage("Nepodarilo se nacist potrebna data", self::FM_ERROR);
	    // TODO LOG
	    $this->redirect("default");
	}
	return $form;
    }

    public function createComponentEventsGrid($name) {

	$eventTypes = [null => null] + EventType::getOptions();

	$grid = new Grid($this, $name);
	$grid->setModel($this->eventsService->getEventsDataSource());

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('title', 'Titulek')
		->setTruncate(20)
		->setSortable()
		->setFilterText();
	$headerTitle = $grid->getColumn('title')->headerPrototype;
	$headerTitle->class[] = 'center';

	$grid->addColumnText('eventType', 'Typ')
		->setSortable()
		->setFilterSelect($eventTypes);
	//$grid->getColumn('role')->setCustomRender(callback($this, 'roleParColToString'));
	$headerType = $grid->getColumn('eventType')->headerPrototype;
	$headerType->class[] = 'center';

	$grid->addColumnDate('takePlaceSince', 'Od')
		->setSortable()
		->setFilterDateRange();
	$headerSince = $grid->getColumn('takePlaceSince')->headerPrototype;
	$headerSince->class[] = 'center';

	$grid->addColumnDate('takePlaceTill', 'Do')
		->setSortable()
		->setFilterDateRange();
	$headerTill = $grid->getColumn('takePlaceTill')->headerPrototype;
	$headerTill->class[] = 'center';

	$grid->addColumnDate('confirmUntil', 'Potvrdit')
		->setSortable();
	$headerDead = $grid->getColumn('confirmUntil')->headerPrototype;
	$headerDead->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deleteEvent!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateEvent')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-events" . date("Y-m-d H:i:s", time()));
    }

}
