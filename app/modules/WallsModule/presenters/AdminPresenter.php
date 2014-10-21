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

namespace App\WallsModule\Presenters;

use \App\SystemModule\Presenters\SecuredPresenter,
    \App\Model\Service\WallService,
    \App\Model\Entities\WallPost,
    \App\WallsModule\Forms\WallPostForm,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Misc\Enum\ArticleStatus,
    \App\Model\Misc\Enum\CommentMode,
    \Nette\Application\UI\Form,
    \Nette\Utils\ArrayHash,
    \Grido\Grid;

/**
 * AdminPresenter of wallposts module
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {

    /**
     * @inject
     * @var \App\WallsModule\Model\Service\IWallService
     */
    public $wallService;
    
    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $usersService;
    
    /**
     * @inject 
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;
    
    public function actionDefault() { // grid render
	
    }
    
    public function actionAddWallPost() { // form render
    }
    
    public function actionUpdateWallPost($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $wpDb = $this->wallService->getWallPost($id);
	    if ($wpDb !== null) {
		$form = $this->getComponent("updateWallPostForm");
		$grArr = $wpDb->getGroups()->map(function($e){return $e->getId();})->toArray();
		$wpDb->setGroups($grArr);
		$form->setDefaults($wpDb->toArray());
	    }
	} catch (Exception $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function createWallPost(ArrayHash $values) {
	try {
	    $wp = new WallPost((array) $values);
	    $wp->setEditor($this->getUser()->getIdentity());
	    $this->wallService->createWallPost($wp);
	} catch (Exception $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function updateWallPost(ArrayHash $values) {
	try {
	    $wp = new WallPost((array) $values);
	    $wp->setEditor($this->getUser()->getIdentity());
	    $this->wallService->updateWallPost($wp);
	} catch (Exception $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function handleDeleteWallPost($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $wpDb = $this->wallService->getWallPost($id);
	    if ($wpDb !== null) {
		$this->wallService->removeWallPost($wpDb->getId());
	    }
	} catch (Exception $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function createComponentAddWallPostForm($name) {
	$form = $this->prepareWallPostForm($name);
	$form->initialize();
	return $form;
    }
    
    public function createComponentUpdateWallPostForm($name) {
	$form = $this->prepareWallPostForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    private function prepareWallPostForm($name) {
	$form = new WallPostForm($this, $name, $this->getTranslator());
	try {
	    $users = $this->usersService->getSelectUsers();
	    $form->setUsers($users);
	    $sGroups = $this->sportGroupService->getSelectSportGroups();
	    $form->setSportGroups($sGroups);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
	return $form;
    }
    
    public function wallPostFormSubmitted(Form $form) {
	$values = $form->getValues();
	try {
	    switch($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createWallPost($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateWallPost($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $form->addError("wallsModule.admin.wallpostForm.errors.wallPostAlreadyExist");
	}
    }
    
    public function createComponentWallPostsGrid($name) {
	$articleStates = [null=>null]+ArticleStatus::getOptions();
	$commentModes = [null=>null]+CommentMode::getOptions();
	
	try {
	    $users = [null=>null]+$this->usersService->getSelectUsers();    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
	
	$grid = new Grid($this, $name);
	$grid->setModel($this->wallService->getWallPostsDatasource());
	$grid->setPrimaryKey("id");
	
	$grid->addColumnNumber("id", "#")
		->cellPrototype->class[] = "center";
	$headerId = $grid->getColumn("id")->headerPrototype;
	$headerId->class[] = "center";
	$headerId->rowspan = "2";
	$headerId->style["width"] = '0.1%';
	
	$grid->addColumnText('title', 'Titulek')
		->setTruncate(20)
		->setSortable()
		->setFilterText();
	$headerTitle = $grid->getColumn('title')->headerPrototype;
	$headerTitle->class[] = 'center';
	
	$grid->addColumnText('status', 'Stav')
		->setSortable()
		->setFilterSelect($articleStates);
	$grid->getColumn('status')->setCustomRender(callback($this, 'stateRenderer'));
	$headerStatus = $grid->getColumn('status')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('commentMode', 'Komentáře')
		->setSortable()
		->setFilterSelect($commentModes);
	$grid->getColumn('commentMode')->setCustomRender(callback($this, 'commentModeRenderer'));
	$headerStatus = $grid->getColumn('commentMode')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('author', 'Autor')
		->setSortable()
		->setFilterSelect($users);
	$headerAuthor = $grid->getColumn('author')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addColumnDate('updated', 'Změna', self::DATETIME_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerAuthor = $grid->getColumn('updated')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addActionHref('delete', '[Smaz]', 'deleteWallPost!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateWallPost')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-wallposts" . date("Y-m-d H:i:s", time()));

	return $grid;
    }
    
    public function stateRenderer($e) {
	$articleStates = ArticleStatus::getOptions();
	return $articleStates[$e->getStatus()];
    }
    
    public function commentModeRenderer($e) {
	$commentModes = CommentMode::getOptions();
	return $commentModes[$e->getCommentMode()];
    }
    
}