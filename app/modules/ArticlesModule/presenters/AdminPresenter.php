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

namespace App\ArticlesModule\Presenters;

use \App\SystemModule\Presenters\SystemAdminPresenter,
    \App\Model\Entities\Article,
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Enum\FormMode,
    \App\ArticlesModule\Forms\ArticleForm,
    \App\Model\Misc\Enum\ArticleStatus,
    \App\Model\Misc\Enum\CommentMode,
    \Nette\Utils\ArrayHash,
    \Nette\Application\UI\Form,
    \Grido\Grid;

/**
 * ArticleAdminPresenter
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SystemAdminPresenter {
    
    /**
     * @inject 
     * @var \App\ArticlesModule\Model\Service\IArticleService
     */
    public $articleService;
    
    /**
     * @inject 
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;
    
    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $usersService;
    
    public function actionDefault() {
    }
    
    public function actionAddArticle() {
	// form render
    }
    
    public function actionUpdateArticle($id) {
	if (!is_numeric($id)) $this->handleBadArgument ($id);
	try {
	    $artDb = $this->articleService->getArticle($id);
	    if ($artDb !== null) {
		$form = $this->getComponent("updateArticleForm");
		$grArr = $artDb->getGroups()->map(function($e){return $e->getId();})->toArray();
		$artDb->setGroups($grArr);
		$form->setDefaults($artDb->toArray());
		$this->template->article = $artDb;
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
    }
    
    public function handleDeleteArticle($id) {
	if (!is_numeric($id)) $this->handleBadArgument($id);
	$this->doDeleteArticle($id);
	if (!$this->isAjax()) {
	    $this->redirect("this");
	}
    }
    
    private function doDeleteArticle($id) {
	try {
	    $this->articleService->deleteArticle($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($id, "this", $ex);
	}
    }
    
    
    public function createArticle(ArrayHash $values) {
	try {
	    $a = new Article((array)$values);
	    $a->setEditor($this->getUser()->getIdentity());
	    $this->articleService->createArticle($a);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function updateArticle(ArrayHash $values) {
	try {
	    $a = new Article((array)$values);
	    $a->setEditor($this->getUser()->getIdentity());
	    $this->articleService->updateArticle($a);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	$this->redirect("default");
    }
    
    public function createComponentAddArticleForm($name) {
	$form = $this->prepareArticleForm($name);
	$form->initialize();
	return $form;
    }
    
    public function createComponentUpdateArticleForm($name) {
	$form = $this->prepareArticleForm($name);
	$form->setMode(FormMode::UPDATE_MODE);
	$form->initialize();
	return $form;
    }
    
    private function prepareArticleForm($name) {
	$form = new ArticleForm($this, $name, $this->getTranslator());
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
    
    public function articleFormSubmitted(Form $form) {
	$values = $form->getValues();
	try {
	    switch ($form->getMode()) {
		case FormMode::CREATE_MODE:
		    $this->createArticle($values);
		    break;
		case FormMode::UPDATE_MODE:
		    $this->updateArticle($values);
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $form->addError("articlesModule.admin.articleForm.errors.articleAlreadyExist");
	}
    }
    
    public function createComponentArticlesGrid($name) {
	
	$articleStates = [null=>null]+ArticleStatus::getOptions();
	$commentModes = [null=>null]+CommentMode::getOptions();
	
	try {
	    $users = [null=>null]+$this->usersService->getSelectUsers();    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
	
	$grid = new Grid($this, $name);
	$grid->setModel($this->articleService->getArticlesDataSource());
	$grid->setTranslator($this->getTranslator());
	$grid->setPrimaryKey("id");
	
	$grid->addColumnNumber("id", "#")
		->cellPrototype->class[] = "center";
	$headerId = $grid->getColumn("id")->headerPrototype;
	$headerId->class[] = "center";
	$headerId->rowspan = "2";
	$headerId->style["width"] = '0.1%';
	
	$grid->addColumnText('title', $this->tt("articlesModule.admin.grid.title"))
		->setSortable()
		->setCustomRender($this->titleRender)
		->setFilterText();
	$headerTitle = $grid->getColumn('title')->headerPrototype;
	$headerTitle->class[] = 'center';
	
	$grid->addColumnText('status', $this->tt("articlesModule.admin.grid.state"))
		->setSortable()
		->setCustomRender($this->statusRender)
		->setFilterSelect($articleStates);
	
	$headerStatus = $grid->getColumn('status')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('commentMode', $this->tt("articlesModule.admin.grid.comments"))
		->setSortable()
		->setCustomRender($this->commentModeRender)
		->setFilterSelect($commentModes);
	$headerStatus = $grid->getColumn('commentMode')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('author', $this->tt("articlesModule.admin.grid.author"))
		->setSortable()
		->setFilterSelect($users);
	$headerAuthor = $grid->getColumn('author')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addColumnDate('updated', $this->tt("articlesModule.admin.grid.change"), self::DATETIME_FORMAT)
		->setSortable()
		->setFilterDateRange();
	$headerAuthor = $grid->getColumn('updated')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addActionHref('delete', '', 'deleteArticle!')
		->setIcon('trash')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("articlesModule.admin.grid.delete")]))
		->setConfirm(function($u) {
		    return $this->tt("articlesModule.admin.grid.rlyDelArticle",null, ["id"=>$u->getId()]);
		});
	
	$grid->addActionHref('edit', '', 'updateArticle')
		->setIcon('pencil')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("articlesModule.admin.grid.update")]));
	
	$grid->addActionHref("goto", "","goToArticle")
		->setIcon('eye-open')
		->setElementPrototype(\Nette\Utils\Html::el("a")->addAttributes(["title"=>$this->tt("articlesModule.admin.grid.view")]));

	$grid->setOperation(["delete" => $this->tt("system.common.delete")], $this->articlesGridOperationHandler);
	
	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-articles" . date("Y-m-d H:i:s", time()));

	return $grid;
    }
    
    public function actionGoToArticle($id) {
	$this->redirect("Public:showArticle", $id);
    }
    
    public function titleRender($e) {
	return \Nette\Utils\Html::el("span")
		->addAttributes(["title"=>$e->getTitle()])
		->setText(\Nette\Utils\Strings::truncate($e->getTitle(), 20));
    }
    
    public function statusRender($e) {
	return $this->tt(ArticleStatus::getOptions()[$e->getStatus()]);
    }
    
    public function commentModeRender($el) {
	return $this->tt(CommentMode::getOptions()[$el->getCommentMode()]);
    }
    
    public function articlesGridOperationHandler($operation, $ids) {
	switch($operation) {
	    case "delete":
		foreach ($ids as $id) {
		    $this->doDeleteArticle($id);
		}
		break;
	}
	$this->redirect("this");
    }
    
    public function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("articlesModule.admin.articleAdd",":Articles:Admin:addArticle");
	$c->addNode("systemModule.navigation.back",":System:Default:adminRoot");
	return $c;
    }
    
    public function createComponentBackSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$c->addNode("systemModule.navigation.back",":Articles:Admin:default");
	return $c;
    }
}
