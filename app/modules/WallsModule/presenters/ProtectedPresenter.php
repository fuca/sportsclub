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
    \Nette\Utils\ArrayHash,
    \Nette\Utils\DateTime,
    \App\Model\Entities\WallpostComment;
    

/**
 * ProtectedPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ProtectedPresenter extends SecuredPresenter {

    /**
     * @inject
     * @var \App\WallsModule\Model\Service\WallService
     */
    public $wallService;
    
    /**
     * @Secured
     */
    public function actionDefault($abbr = null) {
	try {
	    $wps = $this->wallService->getWallPosts();
	    $this->template->wallPosts = $wps;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }

    /**
     * @Secured
     */
    public function actionShowWallPost($id) {
	if ($id === null) {
	    $this->flashMessage("Argument id was null");
	    $this->redirect("default");
	}
	try {
	    // TODO check whether id is numeric or string to call appropriate method
	    $wp = [];
	    $wp = $this->wallService->getWallPost($id);
	    if ($wp !== null) {
		$this->setEntity($wp);
	    }
	    $this->template->data = $wp;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
    }

    public function addComment(ArrayHash $values) {
	try {
	    $comment = new WallpostComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setAuthor($comment->getEditor());
	    $comment->setCreated(new DateTime());
	    $comment->setUpdated(new DateTime());
	    $this->wallService->createComment($comment, $this->getEntity());	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
	if (!$this->isAjax()) {
	    $this->redirect("this");
	} else {
	    $this->redrawControl("commentsData");
	}
    }
    
    public function updateComment(ArrayHash $values) {
	try {
	    $comment = new WallpostComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setUpdated(new DateTime());
	    $this->wallService->updateComment($comment, $this->getEntity());	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleException($ex);
	}
	
	if (!$this->isAjax()) {
	    $this->redirect("this");
	} else {
	    $this->redrawControl("commentsData");
	}
    }
    
    public function deleteComment(WallPostComment $comm) {
	$this->wallService->deleteComment($comm, $this->getEntity());
	if ($this->isAjax()) {
	    $this->redrawControl("commentsData");
	} else {
	    $this->redirect("this");
	}
    }

}
