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

namespace App\PartnersModule\Presenters;

use \Grido\Grid,
    \App\Model\Entities\Partner,
    \App\Model\Misc\Enum\FormMode,
    \Nette\ArrayHash,
    \App\SystemModule\Presenters\SystemAdminPresenter,
    \App\Model\Misc\Exceptions,
    \Nette\Application\UI\Form,
    \App\PartnersModule\Forms\PartnerForm,
    \App\SecurityModule\Model\Misc\Annotations\Secured;

/**
 * Partners module admin presenter
 * @Secured(resource="PartnersAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SystemAdminPresenter {

    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $usersService;
    
    /**
     * @inject
     * @var \App\PartnersModule\Model\Service\IPartnerService
     */
    public $partnerService;

    public function getUsersService() {
	return $this->usersService;
    }
    
    public function getPartnerService() {
	return $this->partnerService;
    }
    
    /**
     * Action for displaying grid with Partners
     * @Secured(resource="default")
     */
    public function actionDefault() {
    }
    
    /**
     * Action for displaying of form for create new partner
     * @Secured(resource="createPartner")
     */
    public function actionCreatePartner() {
    }

    /**
     * Top-down handler for create partner entry
     * @param \Nette\ArrayHash $values
     */
    public function createPartnerHandle(ArrayHash $values) {
	$partner = new Partner((array) $values);
	try {
	    $partner->setEditor($this->getUser()->getIdentity());
	    $this->getPartnerService()->createPartner($partner);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }

    /**
     * Action for displaying update form
     * @Secured(resource="updatePartner")
     */
    public function actionUpdatePartner($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument($id);
	try {
	    $dbPartner = $this->getPartnerService()->getPartner($id, false);
	    if ($dbPartner !== null) {
		$form = $this->getComponent('updatePartnerForm');
		$form->setDefaults($dbPartner->toArray());
	    }
	    $this->template->partner = $dbPartner;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "this", $ex);
	}
    }

    /**
     * Top-down handler for update partner entry
     * @param ArrayHash $values
     */
    public function updatePartnerHandle(ArrayHash $values) {
	$partner = new Partner((array) $values);
	try {
	    $partner->setEditor($this->getUser()->getIdentity());
	    $this->getPartnerService()->updatePartner($partner);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }

    /**
     * Partner delete signal handler
     * @Secured(resource="deletePartner")
     */
    public function handleDeletePartner($id) {
	if (!is_numeric($id))
	    $this->handleBadArgument($id);
	$this->doDeletePartner($id);
	$this->redirect("this");
    }
    
    private function doDeletePartner($id) {
	try {
	    $this->getPartnerService()->deletePartner($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }

    /**
     * Add partner form factory
     * @param string $name
     * @return PartnerForm
     */
    public function createComponentAddPartnerForm($name) {
	$form = $this->preparePartnerForm($name);
	$form->initialize();
	return $form;
    }

    /**
     * Update partner form factory
     * @param string $name
     * @return PartnerForm
     */
    public function createComponentUpdatePartnerForm($name) {
	$form = $this->preparePartnerForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }

    private function preparePartnerForm($name) {
	$form = new PartnerForm($this, $name, $this->getTranslator());
	try {    
	    $users = $this->getUsersService()->getSelectUsers();
	    $form->setUsers($users);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	return $form;
    }

    /**
     * PartnerForm success event handler
     * @param Form $form
     */
    public function partnerFormSuccessHandle(Form $form) {
	$values = $form->getValues();
	try {
	    switch($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createPartnerHandle($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updatePartnerHandle($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $form->addError("partnersModule.partnerForm.partnerNameAlreadyExist");
	}
    }

    /**
     * Partners grid factory
     * @param string $name
     */
    public function createComponentPartnersGrid($name) {

	try {
	    $users = [null => null] + $this->getUsersService()->getSelectUsers();
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}

	$grid = new Grid($this, $name);
	$grid->setTranslator($this->getTranslator());
	$grid->setModel($this->getPartnerService()->getPartnersDataSource());
	$grid->setPrimaryKey("id");

	$grid->addColumnNumber('id', '#')
		->cellPrototype->class[] = 'center';
	$headerId = $grid->getColumn('id')->headerPrototype;
	$headerId->class[] = 'center';
	$headerId->rowspan = "2";
	$headerId->style['width'] = '0.1%';
	
	$grid->addColumnText('name', 'partnersModule.admin.grid.name')
		->setSortable()
		->setCustomRender($this->nameRender);
	$headerName = $grid->getColumn('name')->headerPrototype;
	$headerName->class[] = 'center';

    //	$grid->addColumnText('referrer','partnersModule.admin.grid.referrer')
    //		->setSortable()
    //		->setFilterSelect($users);
    //	$headerRef = $grid->getColumn('referrer')->headerPrototype;
    //	$headerRef->class[] = 'center';

	$grid->addColumnDate('updated', 'partnersModule.admin.grid.updated', self::DATE_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerUpdated = $grid->getColumn('updated')->headerPrototype;
	$headerUpdated->class[] = 'center';

	$y = $this->tt("system.common.yes");
	$n = $this->tt("system.common.no");
	$activeList = [null => null, true => $y, false => $n];
	$grid->addColumnNumber('active', 'partnersModule.admin.grid.active')
		->setReplacement(
		    [true => $y, 
		    null => $n])
		->setSortable()
		->setFilterSelect($activeList);
	$headerActive = $grid->getColumn('active')->headerPrototype;
	$headerActive->class[] = 'center';
	
	$grid->addColumnText('note', 'partnersModule.admin.grid.note')
		->setTruncate(30)
		->setSortable();
	$headerNote = $grid->getColumn('note')->headerPrototype;
	$headerNote->class[] = 'center';

	
	
	// actions

	$grid->addActionHref('delete', '', 'deletePartner!')
		->setIcon('trash')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("partnersModule.admin.grid.delete")]))
		->setConfirm(function($u) {
		    return $this->tt("partnersModule.admin.grid.messages.rlyDelPartner", null, ["id" => $u->getId()]);
		});

	$grid->addActionHref('edit', '', 'updatePartner')
		->setIcon('pencil')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("partnersModule.admin.grid.update")]));

	$grid->setOperation(["delete" => $this->tt("system.common.delete")], $this->partnersGridOpsHandler)
		->setConfirm("delete", $this->tt("partnersModule.admin.grid.messages.rlyDelPartnerItems"));
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-partners " . date("Y-m-d H:i:s", time()));
    }
    
    /**
     * Grid column render
     * @param Partner $e
     * @return string
     */
    public function nameRender($e) {
	return \Nette\Utils\Html::el("a")->setText($e->getName())->addAttributes(["href"=>$e->getLink()]);
    }

    /**
     * Partners grid operations handler
     * @param string $op
     * @param array $ids
     */
    public function partnersGridOpsHandler($op, $ids) {
	switch ($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeletePartner($id);
		}
		break;
	}
	$this->redirect("this");
    }
    
    /**
     * Component sub menu factory
     * @param string $name
     * @return \App\Components\MenuControl
     */
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("partnersModule.admin.partnerAdd", ":Partners:Admin:addPartner");
	$c->addNode("systemModule.navigation.back", ":System:Default:adminRoot");
	return $c;	
    }
    
    
    /**
     * Component back-only sub menu factory
     * @param string $name
     * @return \App\Components\MenuControl
     */
    public function createComponentBackSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("systemModule.navigation.back",":Partners:Admin:default");
	return $c;
    }
}
