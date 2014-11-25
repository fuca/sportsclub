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

use \App\SystemModule\Presenters\SystemUserPresenter,
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
class UserPresenter extends SystemUserPresenter {
    
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
    
    public function defaults() {
	// grid render
    }
    
    public function createComponentUserEventsGrid($name) {
	
	$eventTypes = [null => null] + EventType::getOptions();
	$partyTypes = [null => null] + EventParticipationType::getOptions();
	
	$grid = new Grid($this, $name);
	$grid->setModel($this->eventService->getUserEventsDataSource($this->getUser()->getIdentity()));
	$grid->setPrimaryKey("id");
	$grid->setTranslator($this->getTranslator());
	
	$grid->addColumnText('title', 'eventsModule.grid.title')
		->setCustomRender($this->titleRender)
		->setTruncate(20)
		->setSortable()
		->setFilterText();
	$headerTitle = $grid->getColumn('title')->headerPrototype;
	$headerTitle->class[] = 'center';
	$headerTitle->style["width"] = "20%";
	
	$grid->addColumnText('eventType', "eventsModule.grid.type")
		->setCustomRender($this->typeRender)
		->setSortable()
		->setFilterSelect($eventTypes);
	$headerType = $grid->getColumn('eventType')->headerPrototype;
	$headerType->class[] = 'center';
	
	$grid->addColumnDate('takePlaceSince', 'eventsModule.grid.takeSince', self::DATETIME_FORMAT)
		->setCustomRender($this->sinceRender)
		->setSortable();
	$headerSince = $grid->getColumn('takePlaceSince')->headerPrototype;
	$headerSince->class[] = 'center';

	$grid->addColumnDate('takePlaceTill', 'eventsModule.grid.takeTill', self::DATETIME_FORMAT)
		->setCustomRender($this->tillRender)
		->setSortable();
	$headerTill = $grid->getColumn('takePlaceTill')->headerPrototype;
	$headerTill->class[] = 'center';
	
	$grid->addColumnText("type", "eventsModule.participation.type")
		->setCustomRender($this->partyTypeRender)
		->setSortable()
		->setFilterSelect($partyTypes);
	$headerPart = $grid->getColumn('type')->headerPrototype;
	$headerPart->class[] = 'center';
	
	$grid->addActionHref("goto", "", "goToEvent")
		->setIcon("eye-open")
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("eventsModule.grid.view")]));
	
	$grid->setFilterRenderType($this->filterRenderType);
	return $grid;
	
    }
    
    public function titleRender($e) {
	return $e->getEvent()->getTitle();
    }
    
    public function typeRender($e) {
	return $this->tt(EventType::getOptions()[$e->getEvent()->getEventType()]);
    }
    
    public function sinceRender($e) {
	return $e->getEvent()->getTakePlaceSince()->format(self::DATETIME_FORMAT);
    }
    
    public function tillRender($e) {
	return $e->getEvent()->getTakePlaceTill()->format(self::DATETIME_FORMAT);
    }
    
    public function partyTypeRender($e) {
	return $this->tt(EventParticipationType::getOptions()[$e->getType()]);
    }
    
    public function actionGoToEvent($id) {
	$participation = $this->eventService->getEventParticipation($id);
	$this->redirect(":Events:Club:showEvent", $participation->getEvent()->getId());
    }
}
