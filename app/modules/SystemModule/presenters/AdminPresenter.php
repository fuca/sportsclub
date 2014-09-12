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
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Entities\SportType,
    \App\Model\Entities\SportGroup,
    \Nette\ArrayHash,
    \App\SystemModule\Presenters\SecuredPresenter,
    \App\Services\Exceptions\DataErrorException,
    \Nette\Application\UI\Form,
    \App\SystemModule\Model\Service\ISportGroupService;

/**
 * System package AdminPresenter
 * @Secured resource={system.admin}
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {

    /**
     * @inject
     * @var App\SystemModule\Model\Service\ISportTypeService
     */
    public $sportTypeService;

    /**
     * @inject
     * @var App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;

    public function getSportGroupService() {
	return $this->sportGroupService;
    }

    public function getSportTypeService() {
	return $this->sportTypeService;
    }
    
    /**
     * @Secured resource={resource.overview} privileges={view}
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
	} catch (DataErrorException $ex) {
	    dd($ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSportType($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu id", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $dbType = $this->sportTypeService->getSportType($id);
	    if ($dbType !== null) {
		$form = $this->getComponent('updateSportTypeForm');
		$form->setDefaults($dbType->toArray());
	    }
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se načíst požadovaná data", self::FM_ERROR);
	}
    }

    public function updateSportType(ArrayHash $values) {
	$type = new SportType();
	$type->fromArray((array) $values);
	try {
	    $this->sportTypeService->updateSportType($type);
	} catch (DataErrorException $ex) {
	    dd($ex);
	}
	$this->redirect("default");
    }

    public function handleDeleteSportType($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát arugmentu id", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $this->sportTypeService->deleteSportType($id);
	} catch (DataErrorException $ex) {
	    switch ($ex->getCode()) {
		case 1000:
		    $sport = $this->getSportTypeService()->getSportType($id)->getName();
		    $this->flashMessage("Nemůžete smazat sport '{$sport}', který je užíván alespoň jednou skupinou", self::FM_ERROR);
		    break;
	    }
	} catch (Exception $ex) {
	    dd($ex);
	}
	$this->redirect("this");
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

	$grid->addColumnText('name', 'Název')
		->setSortable();
	$headerAdded = $grid->getColumn('name')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addColumnText('image', 'Ikona');
	//$grid->getColumn('role')->setCustomRender(callback($this, 'roleParColToString'));

	$headerParent = $grid->getColumn('image')->headerPrototype;
	$headerParent->class[] = 'center';

	$grid->addColumnText('note', 'Poznámka')
		->setSortable();
	$headerAdded = $grid->getColumn('note')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deleteSportType!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateSportType')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-types " . date("Y-m-d H:i:s", time()));
    }

    public function createComponentAddSportTypeForm($name) {
	$form = new SportTypeForm($this, $name);
	//$form->setImages(); // pro tohle si udelat imagesStorageService, ci tak neco
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateSportTypeForm($name) {
	$form = new SportTypeForm($this, $name);
	//$form->setImages();
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    // </editor-fold>
    // <editor-fold desc="Administration of GROUPS">

    public function createSportGroup(ArrayHash $values) {
	$type = new SportGroup();
	$type->fromArray((array) $values);
	try {
	    $this->sportGroupService->createSportGroup($type);
	} catch (DataErrorException $ex) {
	    dd($ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSportGroup($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát arugmentu id", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $dbGroup = $this->sportGroupService->getSportGroup($id);
	    if ($dbGroup !== null) {
		$form = $this->getComponent('updateSportGroupForm');

		$form->setDefaults($dbGroup->toArray());
	    }
	} catch (DataErrorException $ex) {
	    dd($e);
	}
    }

    public function updateSportGroup(ArrayHash $values) {
	$type = new \App\Model\Entities\SportGroup();
	$type->fromArray((array) $values);
	try {
	    $this->sportGroupService->updateSportGroup($type);
	} catch (DataErrorException $ex) {
	    dd($ex);
	}
	$this->redirect("default");
    }

    public function handleDeleteSportGroup($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát arugmentu id", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $this->sportGroupService->deleteSportGroup($id);
	} catch (DataErrorException $ex) {
	    switch ($ex->getCode()) {
		case 1000:
		    $this->flashMessage("Nemůžete smazat skupinu, která je něčím rodičem", self::FM_ERROR);
		    break;
	    }
	}
	$this->redirect("this");
    }

    public function createComponentSportGroupGrid($name) {
	$grid = new Grid($this, $name);

	$grid->setModel($this->getSportGroupService()->getSportGroupsDataSource());
	$grid->setPrimaryKey('id');

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('name', 'Název')
		->setSortable();
	$headerAdded = $grid->getColumn('name')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addColumnText('parent', 'Rodič')
		->setSortable();
	$headerParent = $grid->getColumn('parent')->headerPrototype;
	$headerParent->class[] = 'center';

	$grid->addColumnText('sportType', 'Sport')
		->setSortable();
	$headerType = $grid->getColumn('sportType')->headerPrototype;
	$headerType->class[] = 'center';

	$grid->addColumnText('description', 'Poznámka')
		->setSortable();
	$headerAdded = $grid->getColumn('description')->headerPrototype;
	$headerAdded->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deleteSportGroup!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateSportGroup')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-types " . date("Y-m-d H:i:s", time()));
    }

    public function createComponentAddSportGroupForm($name) {
	$form = new SportGroupForm($this, $name);
	$this->prepareSportGroupForm($form);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateSportGroupForm($name) {
	$form = new SportGroupForm($this, $name);
	$this->prepareSportGroupForm($form, $this->getEntityId());
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    private function prepareSportGroupForm(Form $form, $selfId = null) {
	$form->setPriorities($this->sportGroupService->getPriorities());
	try {
	    $sportGroups = $this->sportGroupService->getSelectSportGroups($selfId);
	    $form->setSportGroups($sportGroups);
	    $sportTypes = $this->sportTypeService->getSelectSportTypes();
	    $form->setSportTypes($sportTypes);
	} catch (Exception $ex) {
	    $this->flashMessage($ex->getMessage(), self::FM_ERROR);
	}
	return $form;
    }

    //</editor-fold>
}
