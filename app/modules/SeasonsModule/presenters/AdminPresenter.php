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
    \App\SystemModule\Presenters\SecuredPresenter,
    \App\Services\Exceptions\DataErrorException,
    \App\Services\Exceptions\DuplicateEntryException,
    \App\Services\Exceptions,
    \Nette\Application\UI\Form,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\Model\Entities\SeasonTax,
    \App\SeasonModule\Forms\SeasonTaxForm,
    \App\Model\Entities\SeasonApplication,
    \App\SeasonModule\Forms\SeasonApplicationForm;

/**
 * Season module admin presenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {

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
     * @var \App\Model\Service\IUserService
     */
    public $usersService;

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
	    $form->addError("Season with given label already exist");
	}
    }

    public function createSeasonHandle(ArrayHash $values) {
	$season = new Season();
	$season->fromArray((array) $values);
	try {
	    // TODO set editor
	    $this->getSeasonService()->createSeason($season);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Season could not be created", self::FM_ERROR);
	    dd($ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSeason($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu id", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $dbSeason = $this->seasonService->getSeason($id);
	    if ($dbSeason !== null) {
		$form = $this->getComponent('updateSeasonForm');
		$form->setDefaults($dbSeason->toArray());
	    }
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se načíst požadovaná data", self::FM_ERROR);
	}
    }

    public function updateSeasonHandle(ArrayHash $values) {
	$season = new Season();
	$season->fromArray((array) $values);
	try {
	    $this->getSeasonService()->updateSeason($season);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se uložit požadované změny", self::FM_ERROR);
	}
	$this->redirect("default");
    }

    public function handleDeleteSeason($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát arugmentu id", self::FM_ERROR);
	    $this->redirect("default");
	}
	try {
	    $this->getSeasonService()->deleteSeason($id);
	} catch (DataErrorException $ex) {
	    switch ($ex->getCode()) {
		case 1000:
		    $season = $this->getSeasonService()->getSeason($id)->getLabel();
		    $this->flashMessage("Nemůžete smazat sezónu '{$season}', která je užívána jinými entitami systému", self::FM_ERROR);
		    break;
	    }
	} catch (Exception $ex) {
	    dd($ex);
	}
	$this->redirect("this");
    }

    public function createComponentAddSeasonForm($name) {
	$form = new SeasonForm($this, $name);
	$form->initialize();
	return $form;
    }

    public function createComponentUpdateSeasonForm($name) {
	$form = new SeasonForm($this, $name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
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
		->setSortable();
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
		->setTruncate(15);
	$headerNote = $grid->getColumn('comment')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addColumnText('current', 'Aktivní')
		->setSortable();
	$headerCurrent = $grid->getColumn('current')->headerPrototype;
	$headerCurrent->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deleteSeason!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateSeason')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-seasons " . date("Y-m-d H:i:s", time()));

	return $grid;
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
	} catch (DuplicateEntryException $ex) {
	    $this->flashMessage("Season tax for this season and group already exist", self::FM_ERROR);
	}
    }

    public function createSeasonTaxHandle(ArrayHash $values) {
	$tax = new SeasonTax((array) $values);
	try {
	    $this->getSeasonTaxService()->createSeasonTax($tax);
	} catch (Exception $ex) {
	    $this->flashMessage("Season tax could not be saved", self::FM_ERROR);
	    dd($ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSeasonTax($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu, '{$id}' předáno.", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $tax = $this->getSeasonTaxService()->getSeasonTax($id);
	    if ($tax !== null) {
		$form = $this->getComponent("updateSeasonTaxForm");
		$form->setDefaults($tax->toArray());
	    } else {
		$this->flashMessage("Season tax with given id does not exist", self::FM_WARNING);
	    }
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Data se nepodařilo načíst", self::FM_ERROR);
	    dd($ex);
	}
    }

    public function updateSeasonTaxHandle(ArrayHash $values) {
	$tax = new SeasonTax((array) $values);
	try {
	    $this->getSeasonTaxService()->updateSeasonTax($tax);
	} catch (DataErrorException $e) {
	    $this->flashMessage("Požadovaná změna nemohla být uložena", self::FM_ERROR);
	    dd($e);
	}
	$this->redirect("default");
    }

    public function handleDeleteSeasonTax($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu, '{$id}' předáno.", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $this->getSeasonTaxService()->deleteSeasonTax($id);
	} catch (Exception $ex) {
	    $this->flashMessage("Požadovaná data nemohla být smazána", self::FM_ERROR);
	    dd($ex);
	}
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
	$form = new SeasonTaxForm($this, $name);
	try {
	    $sportGroups = $this->getSportGroupsService()->getSelectSportGroups();
	    $seasons = $this->getSeasonService()->getSelectSeasons();
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se načíst potřebná data", self::FM_ERROR);
	    $this->redirect("default");
	}
	$form->setSportGroups($sportGroups);
	$form->setSeasons($seasons);
	$form->setMemberShip(true); // TODO MODULE CONFIG
	$form->setCreditsActivated(true); // TODO MODULE CONFIG
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

	$grid->addColumnText('comment', 'Poznámka')
		->setSortable()
		->setTruncate(15);
	$headerNote = $grid->getColumn('comment')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deleteSeasonTax!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateSeasonTax')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-seasons " . date("Y-m-d H:i:s", time()));

	return $grid;
    }

    // </editor-fold>
    // <editor-fold desc="Administration of SEASON APPLICATIONS">

    public function actionCreateSeasonApplication() {
	// render form
    }

    public function createSeasonApplicationHandle(ArrayHash $values) {
	dd($values);
	$app = new SeasonApplication((array) $values);
	dd($app);
	try {
	    $this->getSeasonApplicationService()->createSeasonApplication($app);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Přihláška nemohla být uložena", self::FM_ERROR);
	    dd($ex);
	}
	$this->redirect("default");
    }

    public function actionUpdateSeasonApplication($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu, '{$id}' předáno.", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $app = $this->getSeasonApplicationService()->getSeasonApplication($id);
	    if ($app !== null) {
		$form = $this->getComponent("updateSeasonApplicationForm");

		$form->setDefaults($app->toArray());
	    } else {
		$this->flashMessage("Pokoušíte se upravit entitu s neplatným id");
	    }
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Data nemohla být načtena", self::FM_ERROR);
	    dd($ex);
	}
    }

    public function updateSeasonApplicationHandle(ArrayHash $values) {
	$app = new SeasonApplication((array) $values);
	try {
	    $this->getSeasonApplicationService()->updateSeasonApplication($app);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Požadované změny nemohly být uloženy", self::FM_ERROR);
	    dd($ex);
	}
	$this->redirect("default");
    }

    public function handleDeleteSeasonApplication($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu, '{$id}' předáno.", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $this->getSeasonApplicationService()->deleteSeasonApplication($id);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Požadovaná přihláška nemohla být smazána", self::FM_ERROR);
	}
	$this->redirect("this");
    }
    
    public function seasonApplicationFormSubmitHandle(Form $form) {
	$values = $form->getValues();
	try {
	    switch ($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createSeasonApplicationHandle($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateSeasonApplicationHandle($values);
		    break;
	    }
	} catch (DuplicateEntryException $ex) {
	    $this->flashMessage("Season application for this combination of season - group - user already exist", self::FM_ERROR);
	}
    }

    public function prepareSeasonApplicationForm($name) {
	$form = new SeasonApplicationForm($this, $name);
	try {
	    $seasons = $this->getSeasonService()->getSelectSeasons();
	    $users = $this->getUsersService()->getSelectUsers();
	    $groups = $this->getSportGroupsService()->getSelectSportGroups();
	    $form->setSeasons($seasons);
	    $form->setUsers($users);
	    $form->setSportGroups($groups);
	    $form->setMemberShip(true);
	    $form->setCreditsActivated(true);
	} catch (DataErrorException $ex) {
	    $this->flashMessage("Nepodařilo se načíst potřebná data", self::FM_ERROR);
	    dd($ex);
	}

	// TODO 
	// AJAX po vyberu uzivatele vyjet selectbox s dokumentama k nemu prilozenejma
	// pokud se budou vyuzivat dokumenty, tak by se hodila reuse tech prihlasek
	// nebo zavest prihlasku do klubu jeste ze by byla prihlaska do klubu a s tim paralelne do kazde sezony zvlast
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
	$grid->setModel($this->getSeasonApplicationService()->getSeasonApplicationsDataSource());

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

	$grid->addColumnDate('enrolledTime', 'Podáno')
		->setSortable();
	$headerTill = $grid->getColumn('enrolledTime')->headerPrototype;
	$headerTill->class[] = 'center';

	$grid->addColumnDate('updated', 'Změněno')
		->setSortable();
	$headerTill = $grid->getColumn('updated')->headerPrototype;
	$headerTill->class[] = 'center';

	$grid->addColumnText('comment', 'Poznámka')
		->setSortable()
		->setTruncate(15);
	$headerNote = $grid->getColumn('comment')->headerPrototype;
	$headerNote->class[] = 'center';

	$grid->addActionHref('delete', '[Smaz]', 'deleteSeasonApplication!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateSeasonApplication')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-season-applications " . date("Y-m-d H:i:s", time()));
    }

    // </editor-fold>
}
