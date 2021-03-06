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

use \App\SystemModule\Presenters\SystemAdminPresenter,
    \App\SecurityModule\Model\Misc\Annotations\Secured,
    \App\Model\Misc\Exceptions,
    \App\Model\Entities\WallPost,
    \App\WallsModule\Forms\WallPostForm,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Misc\Enum\WallPostStatus,
    \App\Model\Misc\Enum\CommentMode,
    \Nette\Application\UI\Form,
    \Nette\Utils\ArrayHash,
    \Grido\Grid;

/**
 * AdminPresenter of wallposts module
 * @Secured(resource="WallsAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SystemAdminPresenter {

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
    
    /**
     * Action for render wallposts grid
     * @Secured(resource="default")
     */
    public function actionDefault() { 
    }
    
    /**
     * Action for render add wall post form
     * @Secured(resource="addWallpost")
     */
    public function actionAddWallPost() {
    }
    
    /**
     * Action for render updating wall post form
     * @Secured(resource="updateWallPost")
     */
    public function actionUpdateWallPost($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	try {
	    $wpDb = $this->wallService->getWallPost($id);
	    if ($wpDb !== null) {
		$form = $this->getComponent("updateWallPostForm");
		$grArr = $wpDb->getGroups()
			->map(function($e){return $e->getId();})->toArray();
		$wpDb->setGroups($grArr);
		$form->setDefaults($wpDb->toArray());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }
    
    /**
     * Top-down handler for create wallpost. Wallpost form onSuccess event handler.
     * @param ArrayHash $values
     */
    public function createWallPost(ArrayHash $values) {
	try {
	    $wp = new WallPost((array) $values);
	    $wp->setEditor($this->getUser()->getIdentity());
	    $this->wallService->createWallPost($wp);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "default", $ex);
	}
	$this->redirect("default");
    }
    
    /**
     * Top-down handler for update wallpost. Wallpost form onSuccess event handler.
     * @param ArrayHash $values
     */
    public function updateWallPost(ArrayHash $values) {
	try {
	    $wp = new WallPost((array) $values);
	    $wp->setEditor($this->getUser()->getIdentity());
	    $this->wallService->updateWallPost($wp);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "default", $ex);
	}
	$this->redirect("default");
    }
    
    /**
     * Delete wallpost signal handler
     * @Secured(resource="deleteWallPost")
     */
    public function handleDeleteWallPost($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	$this->doDeleteWallPost($id);
	$this->redirect("this");
    }
    
    private function doDeleteWallPost($id) {
	try {
	    $wpDb = $this->wallService->getWallPost($id);
	    if ($wpDb !== null) {
		$this->wallService->removeWallPost($wpDb->getId());
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }
    
    /**
     * Add wall post form factory
     * @param string $name
     * @return WallPostForm
     */
    public function createComponentAddWallPostForm($name) {
	$form = $this->prepareWallPostForm($name);
	$form->initialize();
	return $form;
    }
    
    /**
     * Update wall post form factory
     * @param string $name
     * @return WallPostForm
     */
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
	    $sGroups = $this->sportGroupService->getSelectAllSportGroups();
	    $form->setSportGroups($sGroups);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	return $form;
    }
    
    /**
     * Wall post form onSuccess handler
     * @param WallPostForm $form
     */
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
    
    /**
     * Wall posts admin grid factory
     * @param name $name
     * @return Grid
     */
    public function createComponentWallPostsGrid($name) {
	
	$articleStates	= [null=>null] + WallPostStatus::getOptions();
	$commentModes	= [null=>null] + CommentMode::getOptions();
	
	try {
	    $users = [null=>null] + $this->usersService->getSelectUsers();    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad(null, "default", $ex);
	}
	
	$grid = new Grid($this, $name);
	$grid->setModel($this->wallService->getWallPostsDatasource());
	$grid->setTranslator($this->getTranslator());
	$grid->setPrimaryKey("id");
	
	$grid->addColumnNumber("id", "#")
		->cellPrototype->class[] = "center";
	$headerId = $grid->getColumn("id")->headerPrototype;
	$headerId->class[] = "center";
	$headerId->rowspan = "2";
	$headerId->style["width"] = '0.1%';
	
	$grid->addColumnText('title', $this->tt("wallsModule.admin.grid.title"))
		->setTruncate(20)
		->setSortable()
		->setFilterText();
	$headerTitle = $grid->getColumn('title')->headerPrototype;
	$headerTitle->class[] = 'center';
	
	
	$grid->addColumnText('status', $this->tt("wallsModule.admin.grid.status"))
		->setSortable()
		->setCustomRender($this->statusRender)
		->setFilterSelect($articleStates);
	$headerStatus = $grid->getColumn('status')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('commentMode', $this->tt("wallsModule.admin.grid.cmntMode"))
		->setCustomRender($this->commentModeRender)
		->setSortable()
		->setFilterSelect($commentModes);
	
	$headerStatus = $grid->getColumn('commentMode')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('author', $this->tt("wallsModule.admin.grid.author"))
		->setSortable()
		->setFilterSelect($users);
	$headerAuthor = $grid->getColumn('author')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addColumnDate('updated', $this->tt("wallsModule.admin.grid.change"), self::DATETIME_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerAuthor = $grid->getColumn('updated')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addActionHref('delete', '', 'deleteWallPost!')
		->setIcon('trash')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("wallsModule.admin.grid.delete")]))
		->setConfirm(function($u) {
		    return $this->tt("wallsModule.admin.grid.messages.rlyDelWallPost", null, ["id"=>$u->getId()]);
		});
	
	$grid->addActionHref('edit', '', 'updateWallPost')
		->setIcon('pencil')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title" => $this->tt("wallsModule.admin.grid.update")]));

	$grid->setOperation(["delete"=>$this->tt("system.common.delete")], $this->wpostGridOpsHandler);
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-wallposts" . date("Y-m-d H:i:s", time()));

	return $grid;
    }
    
    /**
     * Grid column render
     * @param WallPost $e
     * @return string
     */
    public function statusRender($e) {
	return $this->tt(WallPostStatus::getOptions()[$e->getStatus()]);
    }
    
    /**
     * Grid column render
     * @param WallPost $el
     * @return string
     */
    public function commentModeRender($el) {
	return $this->tt(CommentMode::getOptions()[$el->getCommentMode()]);
    }
    
    /**
     * Wall posts admin grid operations handler
     * @param string $op
     * @param array $ids
     */
    public function wpostGridOpsHandler($op, $ids) {
	switch($op) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteWallPost($id);
		}
		break;
	}
	$this->redirect("this");
    }
    
    /**
     * Sub menu control factory
     * @param string $name
     * @return \App\Components\MenuControl
     */
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("wallsModule.admin.wallPostAdd", ":Walls:Admin:addWallPost");
	$c->addNode("systemModule.navigation.back", ":System:Default:adminRoot");
	return $c;
    }
    
    /**
     * Back-only sub menu control factory
     * @param string $name
     * @return \App\Components\MenuControl
     */
    public function createComponentBackWallPostsSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("systemModule.navigation.back", ":Walls:Admin:default");
	return $c;
    }
}