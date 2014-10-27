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

namespace App\SystemModule\Presenters;

use \App\SystemModule\Forms\SportTypeForm,
    \App\SystemModule\Forms\SportGroupForm,
    \Grido\Grid,
    \App\Model\Entities\SportGroup,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Entities\SportType,
    \Nette\ArrayHash,
    \App\SystemModule\Presenters\SecuredPresenter,
    \App\Model\Misc\Exceptions,
    \Nette\Application\UI\Form,
    \App\SecurityModule\Model\Misc\Annotations\Secured;

/**
 * System package AdminPresenter
 * @Secured(resource="SystemAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {

    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportTypeService
     */
    public $sportTypeService;

    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;

    public function getSportGroupService() {
	return $this->sportGroupService;
    }

    public function getSportTypeService() {
	return $this->sportTypeService;
    }
    
    /**
     * @Secured()
     */
    public function actionDefault() {
    }

    // <editor-fold desc="Administration of SPORT TYPES">
    public function actionAddSportType() {
	
    }

    public function createSportType(ArrayHash $values) {
	$type = new SportType();
	$type->fromArray((array) $values);
	try {
	    $this->sportTypeService->createSportType($type);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSportType($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	try {
	    $dbType = $this->sportTypeService->getSportType($id);
	    if ($dbType !== null) {
		$form = $this->getComponent('updateSportTypeForm');
		$form->setDefaults($dbType->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, null, $ex);
	}
    }

    public function updateSportType(ArrayHash $values) {
	$type = new SportType();
	$type->fromArray((array) $values);
	try {
	    $this->sportTypeService->updateSportType($type);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }

    public function handleDeleteSportType($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	$this->doDeleteSportType($id);
	$this->redirect("this");
    }
    
    private function doDeleteSportType($id) {
	try {
	    $this->sportTypeService->deleteSportType($id);
	} catch (Exceptions\DependencyException $ex) {
	    $this->handleDependencyDelete($id, "this", $ex);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }

    public function createComponentSportTypeGrid($name) {
	$grid = new Grid($this, $name);

	$grid->setModel($this->getSportTypeService()->getSportTypeDataSource());
	$grid->setPrimaryKey('id');

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('name', $this->tt("systemModule.admin.grid.name"))
		->setSortable();
	$headerAdded = $grid->getColumn('name')->headerPrototype;
	$headerAdded->class[] = 'center';

	//$grid->addColumnText('image', 'Ikona');
	//$grid->getColumn('role')->setCustomRender(callback($this, 'roleParColToString'));

	//$headerParent = $grid->getColumn('image')->headerPrototype;
	//$headerParent->class[] = 'center';

	$grid->addColumnText('note', $this->tt("systemModule.admin.grid.note"))
		->setSortable();
	$headerAdded = $grid->getColumn('note')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addActionHref('delete', '', 'deleteSportType!')
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("systemModule.admin.grid.messages.rlyDelSport",null,["id"=>$u->getId()]);
		});
		
	$grid->addActionHref('edit', '', 'updateSportType')
		->setIcon('pencil');

	$grid->setOperation(["delete"=>$this->tt("systemModule.admin.grid.delete")], $this->sportsGridOpHandler)
		->setConfirm("delete", $this->tt("systemModule.admin.grid.messages.rlyDelSportItems"));
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-types " . date("Y-m-d H:i:s", time()));
    }
    
    public function sportsGridOpHandler($op, $ids) {
	switch($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteSportType($id);
		}
		break;
	}
	$this->redirect("this");
    }

    public function createComponentAddSportTypeForm($name) {
	$form = $this->prepareSportTypeForm($name);
	//$form->setImages();
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateSportTypeForm($name) {
	$form = $this->prepareSportTypeForm($name);
	//$form->setImages();
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    public function prepareSportTypeForm($name) {
	$form = $form = new SportTypeForm($this, $name, $this->getTranslator());
	return $form;
    }
		
    // </editor-fold>
    // <editor-fold desc="Administration of GROUPS">

    public function createSportGroup(ArrayHash $values) {
	$type = new SportGroup();
	$type->fromArray((array) $values);
	try {
	    $this->sportGroupService->createSportGroup($type);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, null, $ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSportGroup($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	try {
	    $dbGroup = $this->sportGroupService->getSportGroup($id);
	    if ($dbGroup !== null) {
		$form = $this->getComponent('updateSportGroupForm');
		$dbGroup->setPriority($dbGroup->getPriority()-1);
		$form->setDefaults($dbGroup->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }

    public function updateSportGroup(ArrayHash $values) {
	$type = new SportGroup();
	$type->fromArray((array) $values);
	try {
	    $this->sportGroupService->updateSportGroup($type);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, null, $ex);
	}
	$this->redirect("default");
    }

    public function handleDeleteSportGroup($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	$this->doDeleteSportGroup($id);
	$this->redirect("this");
    }
    
    private function doDeleteSportGroup($id) {
	try {
	    $this->sportGroupService->deleteSportGroup($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDependencyDelete($id, "this", $ex);
	}
    }

    public function createComponentSportGroupGrid($name) {
	try {
	    $sportTypes = [null=>null]+$this->sportTypeService->getSelectSportTypes();
	    $sportGroups = [null=>null]+$this->sportGroupService->getSelectAllSportGroups();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, null, $ex);
	}
	
	$grid = new Grid($this, $name);
	
	$grid->setModel($this->getSportGroupService()->getSportGroupsDataSource());
	$grid->setPrimaryKey('id');

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('name', $this->tt("systemModule.admin.grid.name"))
		->setSortable();
	$headerAdded = $grid->getColumn('name')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addColumnText('sportType', $this->tt("systemModule.admin.grid.sport"))
		->setSortable()
		->setFilterSelect($sportTypes);
	$headerType = $grid->getColumn('sportType')->headerPrototype;
	$headerType->class[] = 'center';
	
	$grid->addColumnText('parent', $this->tt("systemModule.admin.grid.parent"))	
		->setSortable()
		->setFilterSelect($sportGroups);
	$headerParent = $grid->getColumn('parent')->headerPrototype;
	$headerParent->class[] = 'center';
	
	$y = $this->tt("system.common.yes");
	$n = $this->tt("system.common.no");
	$activeList = [true => $y, false => $n];
	$grid->addColumnText('activity', $this->tt("systemModule.admin.grid.active"))
		->setSortable()	
		->setReplacement([true => $y, 
		    null => $n])
		->setFilterSelect($activeList);
		
	$headerAct = $grid->getColumn('activity')->headerPrototype;
	$headerAct->class[] = 'center';
	$headerAct->style['width'] = '0.1%';

	$grid->addColumnText('description', $this->tt("systemModule.admin.grid.note"))
		->setSortable();
	$headerAdded = $grid->getColumn('description')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addActionHref('delete', '', 'deleteSportGroup!')
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("systemModule.admin.grid.messages.rlyDelGroup",null,["id"=>$u->getId()]);
		});
	
	$grid->addActionHref('edit', '', 'updateSportGroup')
		->setIcon('pencil');

	$grid->setOperation(["delete"=>$this->tt("systemModule.admin.grid.delete")], $this->sportGroupOpHandler)
		->setConfirm("delete", $this->tt("systemModule.admin.grid.messages.rlyDelGroupItems"));
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-types " . date("Y-m-d H:i:s", time()));
    }
    
    public function sportGroupOpHandler($op, $ids) {
	switch($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteSportGroup($id);
		}
 		break;
	}
	$this->redirect("this");
    }

    public function createComponentAddSportGroupForm($name) {
	$form = $this->prepareSportGroupForm($name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateSportGroupForm($name) {
	$form = $this->prepareSportGroupForm($name, $this->getEntityId());
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    private function prepareSportGroupForm($name, $selfId = null) {
	$form = new SportGroupForm($this, $name, $this->getTranslator());
	$form->setPriorities($this->sportGroupService->getPriorities());
	try {
	    $sportGroups = $this->sportGroupService->getSelectAllSportGroups($selfId);
	    $form->setSportGroups($sportGroups);
	    $sportTypes = $this->sportTypeService->getSelectSportTypes();
	    $form->setSportTypes($sportTypes);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->flashMessage($ex->getMessage(), self::FM_ERROR);
	}
	return $form;
    }

    //</editor-fold>
}
