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

use \App\SystemModule\Presenters\SystemAdminPresenter,
    \App\EventsModule\Model\Service\EventService,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\UsersModule\Model\Service\IUserService,
    \App\SecurityModule\Model\Misc\Annotations\Secured,
    \Nette\Utils\ArrayHash,
    \App\Model\Entities\Event,
    \App\EventsModule\Forms\EventForm,
    \App\Model\Misc\Enum\EventType,
    \Grido\Grid,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Misc\Exceptions,
    \App\Forms\BaseForm;

/**
 * AdminEventPresenter
 * @Secured(resource="EventsAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SystemAdminPresenter {

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
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;

    /**
     * @Secured(resource="default")
     */
    public function actionDefault() {
	// render grid
    }

    /**
     * @Secured(resource="addEvent")
     */
    public function actionAddEvent() {
	// render form
    }

    /**
     * @Secured(resource="updateEvent")
     */
    public function actionUpdateEvent($id) {
	try {
	    $e = $this->eventsService->getEvent($id);
	    if ($e !== null) {
		$this->setEntity($e);
		$form = $this->getComponent("updateEventForm");
		$grArr = $e->getGroups()->map(function($e) {
			    return $e->getId();
			})->toArray();
		$e->setGroups($grArr);
		$form->setDefaults($e->toArray());
	    }
	} catch (Exceptions\EntityNotFoundException $ex) {
	    $this->handleEntityNotExists($id, "this", $ex);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }
    
    /**
     * @Secured(resource="updateParticipation")
     */
    public function actionUpdateParticipation($id) {
	try {
	    $e = $this->eventsService->getEvent($id);
	    if ($e !== null) {
		$this->setEntity($e);
	    }
	    $this->template->title = $e->getTitle();
	    $this->template->id = $e->getAlias();
	} catch (Exceptions\EntityNotFoundException $ex) {
	    $this->handleEntityNotExists($id, "this", $ex);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }

    public function createEvent(ArrayHash $values) {
	try {
	    $e = new Event((array) $values);
	    $e->setAuthor($this->getUser()->getIdentity());
	    $this->eventsService->createEvent($e);
	} catch (\Exception $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$this->redirect("default");
    }

    public function updateEvent(ArrayHash $values) {
	try {
	    $e = new Event((array) $values);
	    $e->setEditor($this->getUser()->getIdentity());
	    $this->eventsService->updateEvent($e);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$this->redirect("default");
    }

    /**
     * @Secured(resource="deleteEvent")
     */
    public function handleDeleteEvent($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument($id);
	$this->doDeleteEvent($id);
	$this->redirect("this");
    }

    private function doDeleteEvent($id) {
	try {
	    $this->eventsService->deleteEvent($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
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
	    $groups = $this->sportGroupsService->getSelectAllSportGroups();
	    $form->setSportGroups($groups);
	    $users = $this->userService->getSelectUsers();
	    $form->setUsers($users);
	} catch (\Exception $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	return $form;
    }

    public function createComponentEventsGrid($name) {

	$eventTypes = [null => null] + EventType::getOptions();

	$grid = new Grid($this, $name);
	$grid->setModel($this->eventsService->getEventsDataSource());
	$grid->setTranslator($this->getTranslator());

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
		->setCustomRender($this->typeRender)
		->setSortable()
		->setFilterSelect($eventTypes);

	$headerType = $grid->getColumn('eventType')->headerPrototype;
	$headerType->class[] = 'center';

	$grid->addColumnDate('takePlaceSince', 'Od', self::DATETIME_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerSince = $grid->getColumn('takePlaceSince')->headerPrototype;
	$headerSince->class[] = 'center';

	$grid->addColumnDate('takePlaceTill', 'Do', self::DATETIME_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerTill = $grid->getColumn('takePlaceTill')->headerPrototype;
	$headerTill->class[] = 'center';

	$grid->addColumnDate('confirmUntil', 'Potvrdit', self::DATETIME_FORMAT)
		->setSortable();
	$headerDead = $grid->getColumn('confirmUntil')->headerPrototype;
	$headerDead->class[] = 'center';

	$grid->addActionHref('delete', '', 'deleteEvent!')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("eventsModule.grid.deleteEvent")]))
		->setIcon('trash');
	
	$grid->addActionHref("participation", "", "updateParticipation")
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("eventsModule.grid.updateParticipation")]))
		->setIcon("list-alt");
	
	$grid->addActionHref('edit', '', 'updateEvent')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("eventsModule.grid.updateEvent")]))
		->setIcon('pencil');

	$grid->setOperation(["delete" => "Delete"], $this->eventsGridOperationHandler)
		->setConfirm('delete', 'Are you sure you want to delete %i items?');
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-events" . date("Y-m-d H:i:s", time()));
    }
    
    public function typeRender($el) {
	return $this->tt(EventType::getOptions()[$el->getEventType()]);
    }


    public function eventsGridOperationHandler($operation, $id) {
	switch ($operation) {
	    case "delete":
		foreach ($id as $i) {
		    $this->doDeleteEvent($i);
		}
		$this->redirect("this");
		break;
	    default:
		$this->redirect("this");
	}
    }

    public function createComponentParticipationControl($name) {
	$c = new \App\EventsModule\Components\ParticipationControl($this, $name);
	$c->setEvent($this->getEntity());
	$c->setEventService($this->eventsService);
	$c->setUserService($this->userService);
	return $c;
    }
    
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("eventsModule.admin.eventAdd",":Events:Admin:addEvent");
	$c->addNode("systemModule.navigation.back",":System:Default:adminRoot");
	return $c;
    } 
    
    public function createComponentBackSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("systemModule.navigation.back",":Events:Admin:default");
	return $c;
    } 
}
