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

namespace App\SeasonsModule\Presenters;

use \App\UsersModule\Forms\SportTypeForm,
    \App\UsersModule\Forms\SportGroupForm,
    \Grido\Grid,
    \App\Model\Entities\Season,
    \App\Model\Misc\Enum\FormMode,
    \App\SeasonModule\Forms\SeasonForm,
    \App\Model\Entities\SportType,
    \App\Model\Entities\SportGroup,
    \Nette\ArrayHash,
    \App\SystemModule\Presenters\SystemAdminPresenter,
    \App\Model\Misc\Exceptions,
    \Nette\Application\UI\Form,
    \App\Model\Entities\SeasonTax,
    \App\SeasonModule\Forms\SeasonTaxForm,
    \App\Model\Entities\SeasonApplication,
    \App\SeasonModule\Forms\SeasonApplicationForm,
    \App\SecurityModule\Model\Misc\Annotations\Secured;

/**
 * Season module admin presenter
 * @Secured(resource="SeasonsAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SystemAdminPresenter {

    /**
     * @inject
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    public $seasonService;

    /**
     * @inject
     * @var \App\SeasonsModule\Model\Service\ISeasonTaxService
     */
    public $seasonTaxService;

    /**
     * @inject
     * @var \App\SeasonsModule\Model\Service\ISeasonApplicationService
     */
    public $seasonApplicationService;

    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupsService;

    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $usersService;
    
    /**
     * Array of configuration bindings
     * @var array 
     */
    private $config;
    
    public function setConfig(array $c) {
	$this->config = $c;
    }
    
    private function isMemberShip() {
	return $this->config["memberShip"];
    }

    public function getUsersService() {
	return $this->usersService;
    }

    public function getSeasonApplicationService() {
	return $this->seasonApplicationService;
    }

    public function getSportGroupsService() {
	return $this->sportGroupsService;
    }

    public function getSeasonTaxService() {
	return $this->seasonTaxService;
    }

    public function getSeasonService() {
	return $this->seasonService;
    }

    public function actionDefault() {
	// pak do protected sekce pridat asi prehled prihlasek ci tak neco, nad tim se zamyslet
	// projit servisy tohodle modulu a to je asi vse
	//throw new \Exception();
//	$app = new SeasonApplication();
//	$app->setEditor($this->getUser());
//	$app->setUpdated(new \Nette\Utils\DateTime);
//	$app->setOwner($this->getUser());
//	$app->setSeason(2);
//	$this->seasonApplicationService->createSeasonApplication($app);
    }

// <editor-fold desc="Administration of SPORT SEASONS">

    public function actionAddSeason() {
// form render
    }

    public function seasonFormSubmitHandle(Form $form) {
	$values = $form->getValues();
	try {
	    switch ($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createSeasonHandle($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateSeasonHandle($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $form->addError($this->tt("seasonsModule.seasonForm.errors.labelAlreadyExist", null, ["label" => $values->label]));
	}
    }

    public function createSeasonHandle(ArrayHash $values) {
	$season = new Season((array) $values);
	try {
	    $season->setEditor($this->getUser()->getIdentity());
	    $this->getSeasonService()->createSeason($season);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSeason($id) {
	if (!is_numeric($id)) {
	    $this->handleBadArgument($id);
	}
	try {
	    $dbSeason = $this->seasonService->getSeason($id);
	    if ($dbSeason !== null) {
		$form = $this->getComponent('updateSeasonForm');
		$form->setDefaults($dbSeason->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }

    public function updateSeasonHandle(ArrayHash $values) {
	$season = new Season((array) $values);
	try {
	    $season->setEditor($this->getUser()->getIdentity());
	    $this->getSeasonService()->updateSeason($season);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($season->getId(), "this", $ex);
	}
	$this->redirect("default");
    }

    public function handleDeleteSeason($id) {
	if (!is_numeric($id)) {
	    $this->handleBadArgument($id);
	}
	$this->doDeleteSeason($id);
	$this->redirect("this");
    }

    public function createComponentAddSeasonForm($name) {
	$form = $this->prepareSeasonForm($name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateSeasonForm($name) {
	$form = $this->prepareSeasonForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    private function prepareSeasonForm($name) {
	$form = new SeasonForm($this, $name, $this->getTranslator());
	return $form;
    }

    public function createComponentSeasonsGrid($name) {
	$grid = new Grid($this, $name);
	$grid->setModel($this->getSeasonService()->getSeasonsDataSource());
	$grid->setPrimaryKey('id');

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('label', 'Název')
		->setSortable()
		->setFilterText();
	$headerLabel = $grid->getColumn('label')->headerPrototype;
	$headerLabel->class[] = 'center';

	$grid->addColumnDate('dateSince', 'Od')
		->setSortable();
	$headerSince = $grid->getColumn('dateSince')->headerPrototype;
	$headerSince->class[] = 'center';

	$grid->addColumnDate('dateTill', 'Od')
		->setSortable();
	$headerTill = $grid->getColumn('dateTill')->headerPrototype;
	$headerTill->class[] = 'center';

	$grid->addColumnText('comment', 'Poznámka')
		->setSortable()
		->setTruncate(15)
		->setFilterText();
	$headerNote = $grid->getColumn('comment')->headerPrototype;
	$headerNote->class[] = 'center';

	$y = $this->tt("system.common.yes");
	$n = $this->tt("system.common.no");
	$activeList = [null => null, true => $y, false => $n];
	$grid->addColumnNumber('current', 'Aktivní')
		->setReplacement(
			[true => $y,
			    null => $n])
		->setSortable()
		->setFilterSelect($activeList);
	$headerCurrent = $grid->getColumn('current')->headerPrototype;
	$headerCurrent->class[] = 'center';

	$grid->addActionHref('delete', '', 'deleteSeason!')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("seasonsModule.admin.grid.delete")]))
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("seasonsModule.admin.grid.reallyDeleteSeasonId", null, ["id" => $u->getId()]);
		});
	$grid->addActionHref('edit', '', 'updateSeason')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("seasonsModule.admin.grid.update")]))
		->setIcon('pencil');

	$grid->addActionHref('current', '', 'setSeasonCurrent!')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("seasonsModule.admin.grid.update")]))
		->setIcon('check');

	$grid->setOperation(["delete" => $this->tt("seasonsModule.admin.grid.delete")], null, $this->seasonsGridOperationsHandler);

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-seasons " . date("Y-m-d H:i:s", time()));

	return $grid;
    }

    public function handleSetSeasonCurrent($id) {
	if (!is_numeric($id)) {
	    $this->handleBadArgument($id);
	}
	try {
	    $this->seasonService->setSeasonCurrent($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($id, "this", $ex);
	}
	$this->redirect("this");
    }

    public function seasonsGridOperationsHandler($op, $ids) {
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteSeason($id);
		}
		break;
	}
	$this->redirect("this");
    }

    private function doDeleteSeason($id) {
	try {
	    $this->getSeasonService()->deleteSeason($id);
	} catch (Exceptions\DependencyException $ex) {
	    $this->handleDependencyDelete($id, null, $ex);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, null, $ex);
	}
    }

// </editor-fold>
// <editor-fold desc="Administration of SEASON TAXES">

    public function actionCreateSeasonTax() {
	// render form
    }

    public function seasonTaxFormSubmitHandle(Form $form) {
	$values = $form->getValues();
	try {
	    switch ($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createSeasonTaxHandle($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateSeasonTaxHandle($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $this->logError($ex->getMessage());
	    $form->addError(
		    $this->tt("seasonsModule.admin.errors.seasonGroupExists"), self::FM_WARNING);
	}
    }

    public function createSeasonTaxHandle(ArrayHash $values) {
	$tax = new SeasonTax((array) $values);
	try {
	    $tax->setEditor($this->getUser()->getIdentity());	
    $this->getSeasonTaxService()->createSeasonTax($tax);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleError(null, "this", $ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSeasonTax($id) {
	if (!is_numeric($id)) {
	    $this->handleBadArgument($id);
	}
	try {
	    $tax = $this->getSeasonTaxService()->getSeasonTax($id);
	    if ($tax !== null) {
		$form = $this->getComponent("updateSeasonTaxForm");
		$form->setDefaults($tax->toArray());
	    } else {
		$this->flashMessage(
			$this->tt("seasonsModule.admin.errors.seasonTaxIdDoesntExist", null, ["id" => $id]), self::FM_WARNING);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }

    public function updateSeasonTaxHandle(ArrayHash $values) {
	$tax = new SeasonTax((array) $values);
	try {
	    $tax->setEditor($this->getUser()->getIdentity());
	    $this->getSeasonTaxService()->updateSeasonTax($tax);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }

    public function handleDeleteSeasonTax($id) {
	if (!is_numeric($id)) {
	    $this->handleBadArgument($id);
	}
	$this->doDeleteSeasonTax($id);
	$this->redirect("this");
    }

    public function createComponentAddSeasonTaxForm($name) {
	$form = $this->prepareSeasonTaxForm($name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateSeasonTaxForm($name) {
	$form = $this->prepareSeasonTaxForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    private function prepareSeasonTaxForm($name) {
	$form = new SeasonTaxForm($this, $name, $this->getTranslator());
	try {
	    $sportGroups = $this->getSportGroupsService()->getSelectApplicableGroups();
	    $seasons = $this->getSeasonService()->getSelectSeasons();
	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	$form->setSportGroups($sportGroups);
	$form->setSeasons($seasons);
	if ($this->isMemberShip()) $form->setMemberShip();
	return $form;
    }

    public function createComponentSeasonTaxGrid($name) {
	$grid = new Grid($this, $name);
	$grid->setModel($this->getSeasonTaxService()->getSeasonTaxesDataSource()); // TODO add where season id == given id
	$grid->setPrimaryKey('id');

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('season', 'Sezóna')
		->setSortable();
	$headerLabel = $grid->getColumn('season')->headerPrototype;
	$headerLabel->class[] = 'center';

	$grid->addColumnText('sportGroup', 'Skupina')
		->setSortable();
	$headerSince = $grid->getColumn('sportGroup')->headerPrototype;
	$headerSince->class[] = 'center';

	$grid->addColumnDate('changed', 'Změněno')
		->setSortable();
	$headerTill = $grid->getColumn('changed')->headerPrototype;
	$headerTill->class[] = 'center';
	
	if ($this->isMemberShip()) {
	    $grid->addColumnText('memberShip', 'Člp')
		    ->setSortable();
	    $headerMship = $grid->getColumn('memberShip')->headerPrototype;
	    $headerMship->class[] = 'center';
	}

	$grid->addColumnText('comment', 'Poznámka')
		->setSortable()
		->setTruncate(15);
	$headerNote = $grid->getColumn('comment')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addActionHref('delete', '', 'deleteSeasonTax!')
//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("seasonsModule.admin.grid.delete")]))
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("seasonsModule.admin.grid.reallyDeleteTaxId", null, ["id" => $u->getId()]);
		});
	$grid->addActionHref('edit', '', 'updateSeasonTax')
//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("seasonsModule.admin.grid.update")]))
		->setIcon('pencil');

	$grid->setOperation(
		["delete" => $this->tt("seasonsModule.admin.grid.delete")], $this->taxOperationsGridHandle);
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-seasons " . date("Y-m-d H:i:s", time()));

	return $grid;
    }
    
    private function doDeleteSeasonTax($id) {
	try {
	    $this->getSeasonTaxService()->deleteSeasonTax($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }
    
    public function taxOperationsGridHandle($ops, $ids) {
	switch($ops) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteSeasonTax($id);
		}
		break;
	}
	$this->redirect("this");
    }

// </editor-fold>
// <editor-fold desc="Administration of SEASON APPLICATIONS">

    public function actionCreateSeasonApplication() {
// render form
    }

    public function createSeasonApplicationHandle(ArrayHash $values) {
	$app = new SeasonApplication((array) $values);
	try {
	    $this->getSeasonApplicationService()->createSeasonApplication($app);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($app->getId(), "this", $ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSeasonApplication($id) {
	if (!is_numeric($id)) {
	    $this->handleBadArgument($id);
	}
	try {
	    $app = $this->getSeasonApplicationService()->getSeasonApplication($id);
	    if ($app !== null) {
		$form = $this->getComponent("updateSeasonApplicationForm");
		$form->setDefaults($app->toArray());
	    } else {
		$this->flashMessage(
			$this->tt("seasonsModule.admin.error.seasonAppIdDoesntExist", null, ["id" => $id]));
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }

    public function updateSeasonApplicationHandle(ArrayHash $values) {
	$app = new SeasonApplication((array) $values);
	try {
	    $app->setEditor($this->getUser()->getIdentity());
	    $this->getSeasonApplicationService()->updateSeasonApplication($app);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($app->getId(), "default", $ex);
	}
	$this->redirect("default");
    }

    public function handleDeleteSeasonApplication($id) {
	if (!is_numeric($id)) {
	    $this->handleBadArgument($id);
	}
	$this->doDeleteSeasonApplication($id);
	$this->redirect("this");
    }

    public function seasonApplicationFormSubmitHandle(Form $form) {
	$values = $form->getValues();
	try {
	    switch ($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $users = $values->owner;
		    $values->offsetUnset("owner");
		    foreach ($users as $u) {
			$values["owner"] = $u;
			$this->createSeasonApplicationHandle($values);
		    }
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateSeasonApplicationHandle($values);
		    break;
	    }
	} catch (Exceptions\NoResultException $ex) {
	    $this->logWarning($ex);
	    $form->addError($this->tt("seasonsModule.admin.error.noTaxForSeasonAndGroup"));
	} catch (Exceptions\InvalidStateException $ex) {
	    $this->logWarning("SeasonApplicationForm /// ".$ex);
	    $form->addError($this->tt("seasonsModule.admin.error.appDeadlineExpired"));
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    $form->addError($this->tt("seasonsModule.admin.error.seasonAppUniqueExist"), self::FM_ERROR);
	}
    }

    public function prepareSeasonApplicationForm($name) {
	$form = new SeasonApplicationForm($this, $name, $this->getTranslator());
	try {
	    $seasons = $this->getSeasonService()->getSelectSeasons();
	    $users = $this->getUsersService()->getSelectUsers();
	    $groups = $this->getSportGroupsService()->getSelectApplicableGroups();
	    $form->setSeasons($seasons);
	    $form->setUsers($users);
	    $form->setSportGroups($groups);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	return $form;
    }

    public function createComponentAddSeasonApplicationForm($name) {
	$form = $this->prepareSeasonApplicationForm($name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateSeasonApplicationForm($name) {
	$form = $this->prepareSeasonApplicationForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    public function createComponentSeasonApplicationGrid($name) {

	$grid = new Grid($this, $name);
	$grid->setModel(
		$this->getSeasonApplicationService()
			->getSeasonApplicationsDataSource());

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';

	$grid->addColumnText('owner', 'Člen')
		->setSortable();
	$headerLabel = $grid->getColumn('owner')->headerPrototype;
	$headerLabel->class[] = 'center';

	$grid->addColumnText('season', 'Sezóna')
		->setSortable();
	$headerLabel = $grid->getColumn('season')->headerPrototype;
	$headerLabel->class[] = 'center';

	$grid->addColumnText('sportGroup', 'Skupina')
		->setSortable();
	$headerSince = $grid->getColumn('sportGroup')->headerPrototype;
	$headerSince->class[] = 'center';

	$grid->addColumnDate('enrolledTime', 'Podáno', self::DATETIME_FORMAT)
		->setSortable()
		->setCustomRender($this->appGridUpdatedRender);
	$headerTill = $grid->getColumn('enrolledTime')->headerPrototype;
	$headerTill->class[] = 'center';

	$grid->addColumnDate('updated', 'Změněno')
		->setSortable()
		->setCustomRender($this->appGridUpdatedRender);
	$headerTill = $grid->getColumn('updated')->headerPrototype;
	$headerTill->class[] = 'center';

	$grid->addColumnText('comment', 'Poznámka')
		->setSortable()
		->setTruncate(15);
	$headerNote = $grid->getColumn('comment')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addActionHref('delete', '', 'deleteSeasonApplication!')
//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("seasonsModule.admin.grid.delete")]))
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("seasonsModule.admin.grid.reallyDeleteAppId", null, ["id" => $u->getId()]);
		});

	$grid->addActionHref('edit', '', 'updateSeasonApplication')
//->setElementPrototype(\Nette\Utils\Html::el("span")->addAttributes(["title"=>$this->tt("seasonsModule.admin.grid.update")]))
		->setIcon('pencil');

	$grid->setOperation(
			["delete" => $this->tt("seasonsModule.admin.grid.delete")], null, $this->seasonAppOperationsHandler)
		->setConfirm('delete', $this->tt("usersModule.admin.grid.reallyDeleteItems"));

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-season-applications " . date("Y-m-d H:i:s", time()));
    }
    
    public function appGridUpdatedRender($e) {
	$ed = $e->getEditor();
	return \Nette\Utils\Html::el("span")
		->setText($e->getUpdated()->format(self::DATETIME_FORMAT))
		->addAttributes(["title"=>"Editor: ".$ed]);
    }

    public function seasonAppOperationsHandler($op, $ids) {
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteSeasonApplication($id);
		}
		break;
	}
	$this->redirect("this");
    }

    private function doDeleteSeasonApplication($id) {
	try {
	    $this->getSeasonApplicationService()
		    ->deleteSeasonApplication($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }

// </editor-fold>
    
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("seasonsModule.admin.seasonAdd", ":Seasons:Admin:addSeason");
	$c->addNode("seasonsModule.admin.taxAdd",":Seasons:Admin:addSeasonTax");
	$c->addNode("seasonsModule.admin.appAdd",":Seasons:Admin:addSeasonApplication");
	$c->addNode("systemModule.navigation.back",":System:Default:adminRoot");
	return $c;
    }
    
    public function createComponentBackSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("securityModule.navigation.back", ":Seasons:Admin:default");
	return $c;
    }
}
