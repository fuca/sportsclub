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

namespace App\SystemModule\Components;

use \Nette\Application\UI\Control,
    \App\SystemModule\Forms\LoginForm,
    \Nette\ComponentModel\IContainer,
    \App\Model\Entities\User,
    \Nette\Application\UI\Form,
    \App\SystemModule\Forms\CommentForm,
    \Nette\Security\AuthenticationException,
    \Nette\Application\UI\Link,
    \App\Model\Misc\Enum\FormMode,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Enum\CommentMode,
    \Nette\Security\IIdentity,
    \Doctrine\Common\Collections\ArrayCollection,
    \App\SystemModule\Model\Service\ICommentable;

/**
 * Control for adding comments
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class CommentControl extends Control {

    /**
     * Commenting entity
     * @var \App\SystemModule\Model\Service\ICommentable
     */
    private $entity;

    /**
     * Commenting mode true/false
     * @var boolean predicate if actual user is allowed to add comments for entity
     */
    private $commenting;

    /**
     * Actual user
     * @var \Kdyby\GeneratedProxy\__CG__\App\Model\Entities\User
     */
    private $user;
    
    /**
     * Comment service
     * @var \App\SystemModule\Model\Service\ICommentService
     */
    private $commentService;
    
    private $formTemplate;
    private $dataTemplate;
    private $templatesDir;
    private $adminTemplate;
    
    public function __construct(IContainer $parent = NULL, $name = NULL) {
	parent::__construct($parent, $name);
	$this->templatesDir = __DIR__ . "/templates/";
	$this->dataTemplate = $this->templatesDir . "defaultData.latte";
	$this->formTemplate = $this->templatesDir . "defaultForm.latte";
	$this->adminTemplate = $this->templatesDir . "defaultGrid.latte";
    }

    /**
     * Predicate which checks, if User has rights to creating comments
     * @return boolean
     */
    public function isCommenting() {
	if (!isset($this->commenting)) {
	    $mode = $this->getEntity()->getCommentMode();
	    switch ($mode) {
		case CommentMode::ALLOWED:
		    return true;
		    break;
		case CommentMode::RESTRICTED:
		    return false;
		    break;
		case CommentMode::SIGNED:
		    return $this->getUser()->isLoggedIn();
		    break;
	    }
	}
	return $this->commenting;
    }
    
    public function getUser() {
	if (!isset($this->user))
	    throw new Exceptions\InvalidStateException("Property User is not correctly set, please use appropriate setter first");
	return $this->user;
    }

    public function setUser(User $user) {
	$this->user = $user;
    }
    
    public function getUsers() {
	return $this->users;
    }

    public function setCommenting($isCommenting) {
	$this->isCommenting = $isCommenting;
    }

    public function setFormTemplate($formTemplate) {
	if (!file_exists($this->templatesDir.$formTemplate))
		throw new \Nette\FileNotFoundException("Template file with name '$formTemplate' does not exist");
	$this->formTemplate = $formTemplate;
    }

    public function setDataTemplate($dataTemplate) {
	if (!file_exists($this->templatesDir.$dataTemplate))
		throw new \Nette\FileNotFoundException("Template file with name '$dataTemplate' does not exist");
	$this->dataTemplate = $dataTemplate;
    }

    public function getEntity() {
	if (!isset($this->entity))
	    throw new Exceptions\InvalidStateException("Property Entity is not correctly set, please use appropriate setter first");
	return $this->entity;
    }
    
    function getCommentService() {
	return $this->commentService;
    }

    function setCommentService(\App\SystemModule\Model\Service\ICommentService $commentService) {
	$this->commentService = $commentService;
    }

    public function setEntity(ICommentable $entity) {
	$this->entity = $entity;
    }

    public function render() {
	$this->renderForm();
	$this->renderComments();
    }

    /**
     * Form control render
     */
    public function renderForm() {
	$this->template->setFile($this->formTemplate);
	$this->template->allowed = $this->isCommenting();
	$this->template->titlePlaceHolder = $this->presenter->tt("systemModule.commentControl.titlePlaceHolder");
	$this->template->contentPlaceHolder = $this->presenter->tt("systemModule.commentControl.contentPlaceHolder");
	$this->template->render();
    }

    /**
     * Comments data render
     */
    public function renderComments() {
	$this->template->setFile($this->dataTemplate);
	$cs = $this->getEntity()->getComments();
	$iterator = $cs->getIterator();
	$iterator->uasort(function ($a, $b) {
	    return ($a->getCreated() < $b->getCreated()) ? 1 : -1;
	});	
	$cs = new ArrayCollection(iterator_to_array($iterator));
	$this->template->data = $cs;
	$this->template->userId = $this->getUser()->getId();
	$this->template->render();
    }

    /**
     * CommentForm onSuccess event handler
     * @param Form $form
     */
    public function commentFormSuccess(Form $form) {
	$values = $form->getValues();
	switch ($form->getMode()) {
	    case FormMode::CREATE_MODE:
		if ($this->isCommenting())
		    $this->presenter->addComment($values);
		    break;
	    case FormMode::UPDATE_MODE:
		$this->presenter->updateComment($values);
		break;
	}
	if ($this->presenter->isAjax()) {
	    unset($this->template->showSimpleForm);
	    $this->redrawControl("commentsData");
	}
    }
    
    /**
     * Delete comment signal handler
     * @param numeric $id
     */
    public function handleDeleteComment($id) {
	$coll = $this->getEntity()->getComments();

	$comment = $coll->filter(function ($e) use ($id) {return $e->getId() == $id;})->first();
	
	$this->presenter->deleteComment($comment);
    }
    
    /**
     * Get form signal handler
     * @param numeric $id
     */
    public function handleGetCommentForm($id) {
	$this->template->showSimpleForm = true;
	$form = $this->getComponent("updateCommentForm");
	
	    //$coll = $this->getEntity()->getComments();
	    
	    //$comment = $coll->filter(function ($e) use ($id) {return $e->getId() == $id;})->first();
	    $comment = $this->getCommentService()->getComment($id);
	    
	    $form->setDefaults($comment->toArray());
	
	if ($this->presenter->isAjax()) {
	    $this->invalidateControl('updateCommentForm');
	}
    }
    
    /**
     * Form factory
     * @param string $name
     * @return CommentForm
     */
    public function createComponentAddCommentForm($name) {
	$form = new CommentForm($this, $name, $this->presenter->getTranslator());
	$form->initialize();
	return $form;
    }
    
    /**
     * Form factory
     * @param string $name
     * @return CommentForm
     */
    public function createComponentUpdateCommentForm($name) {
	$form = new CommentForm($this, $name, $this->presenter->getTranslator());
	$form->setMode(FormMode::UPDATE_MODE);
	$form->setShowCancel(true);
	$form->initialize();
	return $form;
    }
    
    public function cancelForm() {
	$this->template->showSimpleForm = false;
	if ($this->presenter->isAjax()) {
	    $this->invalidateControl('updateCommentForm');
	}
    }

}
