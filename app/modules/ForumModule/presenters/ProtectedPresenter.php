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
    \App\Model\Entities\ForumComment,
    \Nette\Utils\ArrayHash,
    \Nette\Utils\DateTime,
    \App\SecurityModule\Model\Misc\Annotations\Secured;
/**
 * ProtectedForumPresenter
 * @Secured(resource="ProtectedForum")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ProtectedPresenter extends SecuredPresenter {
    
    /**
     * @inject
     * @var \App\ForumModule\Model\Service\IForumService
     */
    public $forumService;
    
    public function actionDefault($abbr = self::ROOT_GROUP) {
	$data = null;
	try {
	    $data = $this->forumService->getForums($abbr);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handledataLoad($abbr, "default", $ex);
	}
	$this->template->data = $data;
    }
    
    public function actionShowForum($id) {
	$data = null;
	try {
	    if (is_numeric($id))
		$data = $this->forumService->getForum($id);
	    elseif(is_string($id))
		$data = $this->forumService->getForumAlias($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	$this->setEntity($data);
	$this->template->data = $data;
    }
    
    
    // <editor-fold desc="Comments">
    
    public function addComment(ArrayHash $values) {
	try {
	    $comment = new ForumComment((array) $values);
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
	    $comment = new ForumComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setUpdated(new DateTime());
	    $this->wallService->updateComment($comment, $this->getEntity());	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataSave($values->id, "this", $ex);
	}
	
	if (!$this->isAjax()) {
	    $this->redirect("this");
	} else {
	    $this->redrawControl("commentsData");
	}
    }
    
    public function deleteComment(WallPostComment $comm) {
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
    // </editor-fold>
}
