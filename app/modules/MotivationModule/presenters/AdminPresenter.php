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

use \App\SystemModule\Presenters\SecuredPresenter,
    \App\Model\Misc\Exceptions,
    \Nette\Utils\ArrayHash, 
    \Grido\Grid,
    \Nette\Application\UI\Form,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Entities\MotivationTax,
    \App\Model\Entities\MotivationEntry,
    \App\Model\Misc\Enum\MotivationEntryType,
    \App\MotivationModule\Forms\MotivationEntryForm,
    \App\MotivationModule\Forms\MotivationTaxForm;

/**
 * MotivationAdminPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class AdminPresenter extends SecuredPresenter {
    
    /**
     * @inject
     * @var \App\MotivationModule\Model\Service\IMotivationEntryService
     */
    public $entryService;
    
    /**
     * @inject
     * @var \App\MotivationModule\Model\Service\IMotivationTaxService
     */
    public $taxService;
    
    /**
     * @inject
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    public $seasonService;
    
    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $groupService;
    
    private $selectGroups;
    private $selectSeasons;
    private $selectUsers;
    
    
    public function actionDefault() {
	
    }
    
    // <editor-fold desc="Motivation tax management">
    
    public function actionCreateTax() {
	// render form
    }
    
    public function actionUpdateTax($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	try {
	    $db = $this->taxService->getTax($id);
	    if ($db !== null) {
		$form = $this->getComponent("updateTaxForm");
		$form->setDefaults($db->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}	    
    }
    
    public function createTax(ArrayHash $values) {
	$tax = new MotivationTax((array) $values);
	try {
	    $tax->setEditor($this->getUser()->getIdentity());
	    $this->taxService->createTax($tax);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function updateTax(ArrayHash $values) {
	$tax = new MotivationTax((array) $values);
	try {
	    $tax->setEditor($this->getUser()->getIdentity());
	    $this->taxService->updateTax($tax);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function handleDeleteTax($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	$this->doDeleteTax($id);
	$this->redirect("default");
    }
    
    private function doDeleteTax($id) {
	try {
	    $this->taxService->deleteTax($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($id, "this", $ex);
	}
    }
    
    public function createComponentCreateTaxForm($name) {
	$form = $this->prepareTaxForm($name);
	$form->initialize();
	return $form;
    }
    
    public function createComponentUpdateTaxForm($name) {
	$form = $this->prepareTaxForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    private function prepareTaxForm($name) {
	$form = new MotivationTaxForm($this, $name, $this->getTranslator());
	$form->setSeasons($this->getSelectSeasons());
	$form->setUsers($this->getSelectUsers());
	$form->setSportGroups($this->getSelectSportGroups());
	return $form;
    }
    
    public function motTaxFormSuccessHandle(Form $form) {
	$values = $form->getValues();
	try {
	    switch($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createTax($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateTax($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $form->addError($this->tt("motivationModule.taxForm.messages.taxAlreadyExist"));
	    return;
	}
    }
    
    public function createComponentTaxesGrid($name) {
	try {
	    $seasons = [null=>null]+$this->seasonService->getSelectSeasons();
	    $groups = [null=>null]+$this->groupService->getSelectApplicablegroups();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, self::LAST_CHANCE_REDIRECT, $ex);
	}
	
	$grid = new Grid($this, $name);
	$grid->setModel($this->taxService->getTaxesDataSource());
	$grid->setPrimaryKey("id");
	
	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';
	
	$grid->addColumnText('season', $this->tt("motivationModule.admin.grid.season"))
		->setSortable()
		->setFilterSelect($seasons);
	
	$headerSeas = $grid->getColumn('season')->headerPrototype;
	$headerSeas->class[] = 'center';
	
	$grid->addColumnText('sportGroup', $this->tt("motivationModule.admin.grid.group"))
		->setSortable()
		->setFilterSelect($groups);
	
	$headerGrp = $grid->getColumn('sportGroup')->headerPrototype;
	$headerGrp->class[] = 'center';
	
	
	$grid->addColumnText('credit', $this->tt("motivationModule.admin.grid.credit"))
		->setSortable()
		->setFilterText();
	
	$headerCr = $grid->getColumn('credit')->headerPrototype;
	$headerCr->class[] = 'center';
	
	$grid->addColumnDate("orderedDate", $this->tt("motivationModule.admin.grid.orderedDate"), self::DATE_FORMAT)
		->setSortable();
	$headerOd = $grid->getColumn('orderedDate')->headerPrototype;
	$headerOd->class[] = 'center';
	
	$grid->addColumnText('publicNote', $this->tt("motivationModule.admin.grid.note"))
		->setTruncate(20)
		->setSortable()
		->setFilterText();
	
	$headerNote = $grid->getColumn('publicNote')->headerPrototype;
	$headerNote->class[] = 'center';
	
	$grid->addActionHref("update", "", "updateTax")
		->setIcon("pencil");
	
	$grid->addActionHref('delete', '', "deleteTax!")
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("motivationModule.admin.grid.rlyDeleteTaxId", null, ["id"=>$u->getId()]);
		});
	
	$operation = ['delete' => $this->tt("system.common.delete")];
	$grid->setOperation($operation, $this->entryGridOpsHandler)
		->setConfirm('delete', $this->tt("motivationModule.admin.grid.reallyDeleteItems"));

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-motivation-taxes" . date("Y-m-d H:i:s", time()));
    }
    
    public function taxGridOpsHandler($op, $ids) {
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteTax($id);
		}
		break;
	}
	$this->redirect("this");
    }
    
    // </editor-fold>
    
    // <editor-fold desc="Motivation entry management">
    
    public function actionCreateEntry() {
	// render form
    }
    
    public function actionUpdateEntry($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	try {
	    $db = $this->entryService->getEntry($id);
	    if ($db !== null) {
		$form = $this->getComponent("updateEntryForm");
		$form->setDefaults($db->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}	  
    }
    
    public function createEntry(ArrayHash $values, array $ids) {
	try {
	    foreach ($ids as $id) {
		$values->offsetSet("owner", $id);
		$e = new MotivationEntry((array) $values);
		$e->setEditor($this->getUser()->getIdentity());
		$this->entryService->createEntry($e);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($values->id, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function updateEntry(ArrayHash $values) {
	$e = new MotivationEntry((array) $values);
	try {
	    $e->setEditor($this->getUser()->getIdentity());
	    $this->entryService->updateEntry($e);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($values->id, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function handleDeleteEntry($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	$this->doDeleteEntry($id);
	$this->redirect("this");
    }
    
    private function doDeleteEntry($id) {
	try {
	    $this->entryService->deleteEntry($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "this", $ex);
	}
    }
    
    public function createComponentCreateEntryForm($name) {
	$form = $this->prepareEntryForm($name);
	$form->initialize();
	return $form;
    }
    
    public function createComponentUpdateEntryForm($name) {
	$form = $this->prepareEntryForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    private function prepareEntryForm($name) {
	$form = new MotivationEntryForm($this, $name, $this->getTranslator());
	$form->setSeasons($this->getSelectSeasons());
	$form->setUsers($this->getSelectUsers());
	return $form;
    }
    
    public function motEntryFormSuccessHandle(Form $form) {
	$values = $form->getValues();
	switch($form->getMode()) {
	    case FormMode::CREATE_MODE:
		$ids = $values->offsetGet(MotivationEntryForm::MULTI_OWNER_ID);
		$values->offsetUnset(MotivationEntryForm::MULTI_OWNER_ID);
		$this->createEntry($values, $ids);
		break;
	    case FormMode::UPDATE_MODE:
		$this->updateEntry($values);
		break;
	}
    }
    
    public function createComponentEntriesGrid($name) {

	$seasons = [null=>null]+$this->getSelectSeasons();
	$users = [null=>null]+$this->getSelectUsers();
	
	$grid = new Grid($this, $name);
	$grid->setModel($this->entryService->getEntriesDataSource());
	$grid->setPrimaryKey("id");
	
	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';
	
	$grid->addColumnText('season', $this->tt("motivationModule.admin.grid.season"))
		->setSortable()
		->setFilterSelect($seasons);
	
	$headerSeas = $grid->getColumn('season')->headerPrototype;
	$headerSeas->class[] = 'center';
	
	$grid->addColumnText('owner', $this->tt("motivationModule.admin.grid.owner"))
		->setSortable()
		->setFilterSelect($users);
	
	$headerOwn = $grid->getColumn('owner')->headerPrototype;
	$headerOwn->class[] = 'center';
	
	$grid->addColumnText('amount', $this->tt("motivationModule.admin.grid.amount"))
		->setSortable()
		->setFilterText();
	
	$headerAmnt = $grid->getColumn('amount')->headerPrototype;
	$headerAmnt->class[] = 'center';
	
	$types = MotivationEntryType::getOptions();
	$grid->addColumnText('type', $this->tt("motivationModule.admin.grid.type"))
		->setSortable()
		->setReplacement($types)
		->setFilterSelect([null=>null]+$types);
	
	$headerT = $grid->getColumn('type')->headerPrototype;
	$headerT->class[] = 'center';
	
	$grid->addColumnText('subject', $this->tt("motivationModule.protected.grid.subject"))
		->setSortable()
		->setFilterText();
	
	$headerSubj = $grid->getColumn('subject')->headerPrototype;
	$headerSubj->class[] = 'center';
	
	$grid->addActionHref("update", "", "updateEntry")
		->setIcon("pencil");
	
	$grid->addActionHref('delete', '', "deleteEntry!")
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("motivationModule.admin.grid.rlyDeleteEntryId", null, ["id"=>$u->getId()]);
		});
	
	$operation = ['delete' => $this->tt("system.common.delete")];
	$grid->setOperation($operation, $this->entryGridOpsHandler)
		->setConfirm('delete', $this->tt("motivationModule.admin.grid.reallyDeleteItems"));

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-motivation-entries" . date("Y-m-d H:i:s", time()));
    }
    
    public function entryGridOpsHandler($op, $ids) {
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteEntry($id);
		}
		break;
	}
	$this->redirect("this");
    }
    
    private function getSelectUsers() {
	try {
	    if (!isset($this->selectUsers))
		$this->selectUsers = $this->userService->getSelectUsers();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, self::LAST_CHANCE_REDIRECT, $ex);
	}
	return $this->selectUsers;
    }
    
    private function getSelectSeasons(){
	try {
	    if (!isset($this->selectSeasons))
		$this->selectSeasons = $this->seasonService->getSelectSeasons();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, self::LAST_CHANCE_REDIRECT, $ex);
	}
	return $this->selectSeasons;
    }
    
    private function getSelectSportGroups(){
	try {
	    if (!isset($this->selectGroups))
		$this->selectGroups = $this->groupService->getSelectApplicablegroups();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, self::LAST_CHANCE_REDIRECT, $ex);
	}
	return $this->selectGroups;
    }
    
    // </editor-fold>
    
}
