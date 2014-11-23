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

namespace App\CommunicationModule\Presenters;

use \App\SystemModule\Presenters\SystemAdminPresenter,
    \App\Model\Misc\Enum\FormMode,
    \App\CommunicationModule\Forms\ForumForm,
    \App\Model\Misc\Enum\CommentMode,
    \Nette\Application\UI\Form,
    \Nette\Utils\ArrayHash,
    \App\Model\Entities\Forum,
    \Grido\Grid,
    \App\SecurityModule\Model\Misc\Annotations\Secured;

/**
 * AdminSectionCommunicationPresenter
 * @Secured(resource="ForumAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SystemAdminPresenter {
    
     /**
     * @inject
     * @var \App\CommunicationModule\Model\Service\IForumService
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
	if (!is_numeric($id)) $this->handleBadArgument($id);
	try {
	    $fDb = $this->forumService->getForum($id);
	    if ($fDb !== null) {
		$form = $this->getComponent("updateForumForm");
		$grArr = $fDb->getGroups()
			->map(function($e){return $e->getId();})->toArray();
		$fDb->setGroups($grArr);
		$form->setDefaults($fDb->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }
    
    public function createForum(ArrayHash $values) {
	try {
	    $f = new Forum((array) $values);
	    $f->setEditor($this->getUser()->getIdentity());
	    $this->forumService->createForum($f);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function updateForum(ArrayHash $values) {
	try {
	    $f = new Forum((array) $values);
	    $this->forumService->updateForum($f);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function handleDeleteForum($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	$this->doDeleteForum($id);
	$this->redirect("this");
    }
    
    private function doDeleteForum($id) {
	try {
	    $this->forumService->deleteForum($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
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
	$grid->setTranslator($this->getTranslator());
	$grid->setPrimaryKey("id");
	
	$grid->addColumnNumber("id", "#")
		->cellPrototype->class[] = "center";
	$headerId = $grid->getColumn("id")->headerPrototype;
	$headerId->class[] = "center";
	$headerId->rowspan = "2";
	$headerId->style["width"] = '0.1%';
	
	$grid->addColumnText('title', "communicationModule.admin.grid.title")
		->setTruncate(20)
		->setSortable()
		->setFilterText();
	$headerTitle = $grid->getColumn('title')->headerPrototype;
	$headerTitle->class[] = 'center';
	
	$grid->addColumnText('commentMode', "communicationModule.admin.grid.comments")
		->setCustomRender($this->commentModeRender)
		->setSortable()
		->setFilterSelect($commentModes);
	$headerStatus = $grid->getColumn('commentMode')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('author', "communicationModule.admin.grid.author")
		->setSortable()
		->setFilterSelect($users);
	$headerAuthor = $grid->getColumn('author')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addColumnDate('updated', "communicationModule.admin.grid.change", self::DATETIME_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerAuthor = $grid->getColumn('updated')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addActionHref('delete', '', 'deleteForum!')
		->setIcon('trash')
		->setConfirm(function($u) {
		    return $this->tt("communicationModule.admin.grid.messages.rlyDelForum", null, ["id"=>$u->getId()]);
		});
		
	$grid->addActionHref('edit', '', 'updateForum')
		->setIcon('pencil');

	$grid->setOperation(["delete"=>"communicationModule.admin.grid.delete"], 
		$this->forumGridOpsHandler)
		->setConfirm("delete", "communicationModule.admin.grid.messages.rlyDelForumItems");
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-forum" . date("Y-m-d H:i:s", time()));
	return $grid;
    }
    
    public function commentModeRender($el) {
	return $this->tt(CommentMode::getOptions()[$el->getCommentMode()]);
    }
    
    public function forumGridOpsHandler($op, $ids) {
	switch($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteForum($id);
		}
		break;
	}
	$this->redirect("this");
    }
    
    private function prepareForumForm($name) {
	$form = new ForumForm($this, $name, $this->getTranslator());
	$form->setSportGroups($this->getSelectGroups());
	$form->setUsers($this->getSelectUsers());
	return $form;
    }
    
    private function getSelectGroups() {
	try {
	    $groups = $this->sportGroupService->getSelectAllSportGroups();
	    return $groups;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
    }
    
    private function getSelectUsers() {
	try {
	    $users = $this->userService->getSelectUsers();    
	    return $users;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
    }
    
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("communicationModule.admin.forumAdd",":Communication:Admin:addForum");
	$c->addNode("systemModule.navigation.back",":System:Default:adminRoot");
	return $c;
    }
}
