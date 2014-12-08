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

use \App\SystemModule\Presenters\SystemClubPresenter,
    \App\Model\Misc\Enum\FormMode,
    \App\CommunicationModule\Forms\ForumForm,
    \App\CommunicationModule\Forms\ClubForumThreadForm,
    \App\Model\Misc\Enum\CommentMode,
    \App\Model\Entities\ForumThreadComment,
    \Nette\Application\UI\Form,
    \Nette\Utils\DateTime,
    \Nette\Utils\ArrayHash,
    \App\Model\Entities\Forum,
    \App\Model\Entities\ForumThread,
    \Grido\Grid,
    \App\SecurityModule\Model\Misc\Annotations\Secured;

/**
 * ForumPresenter
 * @Secured(resource="ForumClub")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ForumPresenter extends SystemClubPresenter {
    
    /** @persistent string */
    public $abbr = self::ROOT_GROUP;
    
    /**
     * @inject
     * @var \App\CommunicationModule\Model\Service\IForumService
     */
    public $forumService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;
    
    /**
     * @Secured(resource="default")
     */
    public function actionDefault($abbr = self::ROOT_GROUP) {
	$data = $gDb = null;
	try {
	    $gDb = $this->sportGroupService->getSportGroupAbbr($abbr);
	    if ($gDb !== null) {
		$data = $this->forumService->getForums($gDb);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handledataLoad($abbr, "default", $ex);
	}
	
	$type = $gDb->getSportType();
	    if ($type !== null)
		$type = " ({$type->getName()})";
	    else
		$type = "";
	$groupLabel = $gDb->getName() . $type;
	
	if ($data === null) {
	    $this->redirect("default", [$abbr=>$groupLabel]);
	}
	$this->template->groupLabel = $groupLabel;
	$this->template->abbr = $abbr;
	$this->template->data = $data;
    }
    
    /**
     * @Secured(resource="showForum")
     */
    public function actionShowForum($id, $abbr = self::ROOT_GROUP) {
	$data = $sg = null;
	try {
	    
	    if (is_numeric($abbr))
		$sg = $this->sportGroupService->getSportGroup($abbr);
	    elseif(is_string($abbr))
		$sg = $this->sportGroupService->getSportGroupAbbr($abbr);
	    
	    if (is_numeric($id))
		$data = $this->forumService->getForum($id);
	    elseif(is_string($id))
		$data = $this->forumService->getForumAlias($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	$type = $sg->getSportType();
	    if ($type !== null)
		$type = " ({$type->getName()})";
	    else
		$type = "";
	$groupLabel = $sg->getName() . $type;
	
	if ($data === null) {
	    $this->redirect("default", [$abbr=>$groupLabel]);
	}
	$this->setEntity($data);
	$this->template->groupLabel = $groupLabel;
	$this->template->abbr = $sg->getAbbr();
	
	$this->template->forumTitle = $data->getTitle();
	$this->template->data = $data->getThreads();
    }
    
    /**
     * @Secured(resource="addForumThread")
     */
    public function actionAddForumThread($f, $abbr = self::ROOT_GROUP) {
	$forum = $sg = null;
	try {
	    
	    if (is_numeric($abbr))
		$sg = $this->sportGroupService->getSportGroup($abbr);
	    elseif(is_string($abbr))
		$sg = $this->sportGroupService->getSportGroupAbbr($abbr);
	    
	    if (is_numeric($f))
		$forum = $this->forumService->getForum($f);
	    elseif(is_string($f))
		$forum = $this->forumService->getForumAlias($f);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($abbr, "default", $ex);
	}
	if ($forum === null || $sg === null)
	    $this->redirect("default", [$g]);
	
	$type = $sg->getSportType();
	    if ($type !== null)
		$type = " ({$type->getName()})";
	    else
		$type = "";
	$groupLabel = $sg->getName() . $type;
	$this->setEntity($forum);
	$this->template->groupLabel = $groupLabel;
	$this->template->forumTitle = $forum->getTitle();
	$this->template->forumAlias = $forum->getAlias();
	$this->template->abbr = $abbr;
	$form = $this->getComponent("addThreadForm");
	$form->setDefaults(["forum"=>$forum->getId()]);
    }
    
    public function forumThreadFormSuccess(Form $form) {
	$values = $form->getValues();
	try {
	    switch($form->getMode()) {
		case FormMode::CREATE_MODE:
			$this->createForumThread($values);
		    break;
		case FormMode::UPDATE_MODE:
		    break;
	    }
	} catch (Exceptions\DuplicateEntryException $ex) {
	    $form->addError("communicationModule.club.forumThreadAlreadyExist");
	}
    }
    
    private function createForumThread(ArrayHash $values) {
	$thread = new ForumThread((array) $values);
	try {
	    $thread->setAuthor($this->getUser()->getIdentity());
	    $thread->setCommentMode(CommentMode::ALLOWED);
	    $this->forumService->createForumThread($thread);
	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	$abbr = $this->getParameter("abbr");
	$this->redirect("showThread", [$thread->getAlias(), $abbr]);
    }
    
    /**
     * @Secured(resource="showThread")
     */
    public function actionShowThread($id, $abbr) {
	$thread = $sg = null;
	try {
	     if (is_numeric($abbr))
		$sg = $this->sportGroupService->getSportGroup($abbr);
	    elseif(is_string($abbr))
		$sg = $this->sportGroupService->getSportGroupAbbr($abbr);
	    
	    if (is_numeric($id))
		$thread = $this->forumService->getForumThread($id);
	    elseif(is_string($id))
		$thread = $this->forumService->getForumThreadAlias($id);
	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	
	$type = $sg->getSportType();
	    if ($type !== null)
		$type = " ({$type->getName()})";
	    else
		$type = "";
	$groupLabel = $sg->getName() . $type;
	
	$this->template->groupLabel = $groupLabel;
	$this->template->abbr = $sg->getAbbr();
	$forum = $thread->getForum();
	$this->template->forumTitle = $forum->getTitle();
	$this->template->forumAlias = $forum->getAlias();
	$this->template->thread = $thread;
	$this->setEntity($thread);
    }
    
    
    // <editor-fold desc="Comments">
    
    public function addComment(ArrayHash $values) {
	try {
	    $comment = new ForumThreadComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setAuthor($comment->getEditor());
	    $comment->setCreated(new DateTime());
	    $comment->setUpdated(new DateTime());
	    $this->forumService->createComment($comment, $this->getEntity());	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave(null, "this", $ex);
	}
	if (!$this->isAjax()) {
	    $this->redirect("this");
	} else {
	    $this->redrawControl("commentsData");
	}
    }
    
    public function updateComment(ArrayHash $values) {
	try {
	    $comment = new ForumThreadComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setUpdated(new DateTime());
	    $this->forumService->updateComment($comment, $this->getEntity());	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	
	if (!$this->isAjax()) {
	    $this->redirect("this");
	} else {
	    $this->redrawControl("commentsData");
	}
    }
    
    public function deleteComment(ForumThreadComment $comm) {
	try {
	    $this->forumService->deleteComment($comm, $this->getEntity());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($comm->getId(), "this", $ex);
	}
	
	if ($this->isAjax()) {
	    $this->redrawControl("commentsData");
	} else {
	    $this->redirect("this");
	}
    }
    
 
    protected function createComponentAddThreadForm($name) {
	$c = new ClubForumThreadForm($this, $name, $this->getTranslator());	
	$c->initialize();
	return $c;
    }
    
    protected function createComponentSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$forum = $this->getEntity();
	$abbr = $this->getParameter("abbr");
	$c->addNode("communicationModule.admin.forumThrAdd",":Communication:Forum:addForumThread", true, ["param"=>["f"=>$forum->getId(),"abbr"=>$abbr]]);
	$c->addNode("systemModule.navigation.back",":Communication:Forum:default");
	return $c;
    }
    
    protected function createComponentBackShowForumSubMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.navigation.options");
	$forumAlias = $this->getEntity()->getAlias();
	$abbr = $this->getParameter("abbr");
	$c->addNode("systemModule.navigation.back",":Communication:Forum:showForum", true, ["param"=>["id"=>$forumAlias,"abbr"=>$abbr]]);
	return $c;
    }
    
    // </editor-fold>
}
