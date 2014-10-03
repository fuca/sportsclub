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

use \App\SystemModule\Presenters\SecuredPresenter,
    \App\ArticlesModule\Model\Service\IArticleService,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\Model\Service\IUserService,
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Enum\FormMode,
    \App\Model\Entities\Article,
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
class AdminPresenter extends SecuredPresenter {
    
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
     * @var \App\Model\Service\IUserService
     */
    public $usersService;
    
    public function actionDefault() {
    }
    
    public function actionAddArticle() {
	// form render
    }
    
    public function actionUpdateArticle($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $artDb = $this->articleService->getArticle($id);
	    if ($artDb !== null) {
		$form = $this->getComponent("updateArticleForm");
		$grArr = $artDb->getGroups()->map(function($e){return $e->getId();})->toArray();
		$artDb->setGroups($grArr);
		$form->setDefaults($artDb->toArray());
	    }
	} catch (Exception $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function handleDeleteArticle($id) {
	if (!is_numeric($id)) {
	    $this->flashMessage("Špatný formát argumentu", self::FM_WARNING);
	    $this->redirect("default");
	}
	try {
	    $this->articleService->deleteArticle($id);
	} catch (Exception $ex) {
	    $this->handleException($ex);
	}
	if (!$this->isAjax()) {
	    $this->redirect("this");
	}
    }
    
    public function createArticle(ArrayHash $values) {
	try {
	    $a = new Article((array)$values);
	    $a->setEditor($this->getUser()->getIdentity());
	    $this->articleService->createArticle($a);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }
    
    public function updateArticle(ArrayHash $values) {
	try {
	    $a = new Article((array)$values);
	    $a->setEditor($this->getUser()->getIdentity());
	    $this->articleService->updateArticle($a);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
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
	    $sGroups = $this->sportGroupService->getSelectSportGroups();
	    $form->setSportGroups($sGroups);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
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
	$headerStatus = $grid->getColumn('status')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('commentMode', 'Komentáře')
		->setSortable()
		->setFilterSelect($commentModes);
	$headerStatus = $grid->getColumn('commentMode')->headerPrototype;
	$headerStatus->class[] = 'center';
	
	$grid->addColumnText('author', 'Autor')
		->setSortable()
		->setFilterSelect($users);
	$headerAuthor = $grid->getColumn('author')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addColumnDate('updated', 'Změna')
		->setSortable()
		->setFilterDateRange();
	$headerAuthor = $grid->getColumn('updated')->headerPrototype;
	$headerAuthor->class[] = 'center';
	
	$grid->addActionHref('delete', '[Smaz]', 'deleteArticle!')
		->setIcon('trash');
	$grid->addActionHref('edit', '[Uprav]', 'updateArticle')
		->setIcon('pencil');

	$grid->setFilterRenderType($this->filterRenderType);
	$grid->setExport("admin-articles" . date("Y-m-d H:i:s", time()));

	return $grid;
    }
}
