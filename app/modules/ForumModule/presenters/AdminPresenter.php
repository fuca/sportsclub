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

namespace App\ForumModule\Presenters;

use \App\SystemModule\Presenters\SecuredPresenter,
    \App\Model\Misc\Enum\FormMode,
    \App\ForumModule\Forms\ForumForm,
    \App\Model\Misc\Enum\CommentMode,
    \Nette\Application\UI\Form,
    \Nette\Utils\ArrayHash,
    \App\Model\Entities\Forum,
    \Grido\Grid;

/**
 * AdminForumPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {
    
    /**
     * @inject
     * @var \App\ForumModule\Model\Service\IForumService
     */
    public $forumService;
    
    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\SportGroupService
     */
    public $sportGroupService;
    
    public function actionDefault() { // grid
	
    }
    
    public function actionAddForum() { // form
	
    }
    
    public function actionUpdateForum($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $fDb = $this->forumService->getForum($id);
	    if ($fDb !== null) {
		$form = $this->getComponent("updateForumForm");
		$grArr = $fDb->getGroups()->map(function($e){return $e->getId();})->toArray();
		$fDb->setGroups($grArr);
		$form->setDefaults($fDb->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function createForum(ArrayHash $values) {
	try {
	    $f = new Forum((array) $values);
	    $f->setEditor($this->getUser()->getIdentity());
	    $this->forumService->createForum($f);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function updateForum(ArrayHash $values) {
	try {
	    $f = new Forum((array) $values);
	    $this->forumService->updateForum($f);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function handleDeleteForum($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $this->forumService->deleteForum($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function createComponentAddForumForm($name) {
	$form = $this->prepareForumForm($name);
	$form->initialize();
	return $form;
    }
    
    public function createComponentUpdateForumForm($name) {
	$form = $this->prepareForumForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    public function forumFormSubmitted(Form $form) {
	$values = $form->getValues();
	try {
	    switch($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createForum($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateForum($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $form->addError("forumModule.admin.forumForm.errors.forumAlreadyExist");
	}
    }
    
    public function createComponentForumGrid($name) {
	$commentModes	= [null=>null]+CommentMode::getOptions();
	$users		= [null=>null]+$this->getSelectUsers();
	$grid = new Grid($this, $name);
	
	$grid->setModel($this->forumService->getForumDataSource());
	$grid->setPrimaryKey("id");
	
	$grid->addColumnNumber("id", "#")
		->cellPrototype->class[] = "center";
	$headerId = $grid->getColumn("id")->headerPrototype;
	$headerId->class[] = "center";
	$headerId->rowspan = "2";
	$headerId->style["width"] = '0.1%';
	
	$grid->addColumnText('title', 'Název')
		->setTruncate(20)
		->setSortable()
		->setFilterText();
	$headerTitle = $grid->getColumn('title')->headerPrototype;
	$headerTitle->class[] = 'center';
	
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
	
	$grid->addActionHref('delete', '[Smaz]', 'deleteForum!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateForum')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-forum" . date("Y-m-d H:i:s", time()));
	return $grid;
    }
    
        
    public function commentModeRenderer($e) {
	$commentModes = CommentMode::getOptions();
	return $commentModes[$e->getCommentMode()];
    }
    
    private function prepareForumForm($name) {
	$form = new ForumForm($this, $name, $this->getTranslator());
	$form->setSportGroups($this->getSelectGroups());
	$form->setUsers($this->getSelectUsers());
	return $form;
    }
    
    private function getSelectGroups() {
	try {
	    $groups = $this->sportGroupService->getSelectSportGroups();
	    return $groups;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }
    
    private function getSelectUsers() {
	try {
	    $users = $this->userService->getSelectUsers();    
	    return $users;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }
}
