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

namespace App\MotivationModule\Presenters;
use	
    \App\SystemModule\Presenters\SystemUserPresenter,
    \Grido\Grid,    
    \App\SecurityModule\Model\Misc\Annotations\Secured,
    \App\Model\Misc\Enum\MotivationEntryType;

/**
 * MotivationProtectedPresenter
 * @Secured(resource="MotivationUser")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class ProtectedPresenter extends SystemUserPresenter {
    
    /**
     * @inject
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    public $seasonService;
    
    /**
     * @inject
     * @var \App\MotivationModule\Model\Service\IMotivationEntryService
     */
    public $entryService;
    
    
    /**
     * @Secured(resource="default")
     */
    public function actionDefault() {
	// grid render
    }
    
    public function createComponentUserMotivationGrid($name) {
	try {
	    $seasons = [null=>null]+$this->seasonService->getSelectSeasons();
	    //$users = [null=>null]+$this->userService->getSelectUsers();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, self::LAST_CHANCE_REDIRECT, $ex);
	}
	
	$grid = new Grid($this, $name);
	$grid->setModel($this->entryService->getEntriesDataSource($this->getUser()->getIdentity()));
	$grid->setPrimaryKey("id");
	
//	$grid->addColumnNumber('id', '#')
//		->cellPrototype->class[] = 'center';
//	$headerId = $grid->getColumn('id')->headerPrototype;
//	$headerId->class[] = 'center';
//	$headerId->rowspan = "2";
//	$headerId->style['width'] = '0.1%';
	
	$grid->addColumnText('season', $this->tt("motivationModule.protected.grid.season"))
		->setSortable()
		->setFilterSelect($seasons);
	
	$headerSeas = $grid->getColumn('season')->headerPrototype;
	$headerSeas->class[] = 'center';
	
	
	$grid->addColumnText('amount', $this->tt("motivationModule.protected.grid.amount"))
		->setSortable()
		->setFilterText();
	
	$headerAmnt = $grid->getColumn('amount')->headerPrototype;
	$headerAmnt->class[] = 'center';
	
	$grid->addColumnDate("updated", $this->tt("motivationModule.protected.grid.updated"), self::DATE_FORMAT)
		->setSortable();
	$headerOd = $grid->getColumn('updated')->headerPrototype;
	$headerOd->class[] = 'center';
	
	$grid->addColumnText('type', $this->tt("motivationModule.protected.grid.type"))
		->setSortable()
		->setReplacement(MotivationEntryType::getOptions())
		->setFilterSelect([null=>null]+MotivationEntryType::getOptions());
	
	$headerT = $grid->getColumn('type')->headerPrototype;
	$headerT->class[] = 'center';
	
	$grid->addColumnText('subject', $this->tt("motivationModule.protected.grid.subject"))
		->setSortable()
		->setFilterText();
	
	$headerSubj = $grid->getColumn('subject')->headerPrototype;
	$headerSubj->class[] = 'center';

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("protected-motivation" . date("Y-m-d H:i:s", time()));
    }
}
