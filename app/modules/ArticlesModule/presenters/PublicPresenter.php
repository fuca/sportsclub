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

use \App\SystemModule\Presenters\SystemPublicPresenter,
    \App\Model\Entities\ArticleComment,
    \Nette\Utils\ArrayHash,
    \Nette\Utils\DateTime;
 

/**
 * ArticlePresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class PublicPresenter extends SystemPublicPresenter {
    
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
    
    public function actionDefault($abbr = self::ROOT_GROUP) {
	$data = null;
	$sg = null;
	try {
	    if (is_string($abbr))
		$sg = $this->sportGroupService->getSportGroupAbbr($abbr);
	    elseif (is_numeric($abbr))
		$sg = $this->sportGroupService->getSportGroup($abbr);
	    $data = $this->articleService->getArticles($sg);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($abbr, null, $ex);
	}
	$this->template->data = $data;
    }
    
    public function actionShowArticle($id = null) {
	$data = null;
	try {
	    if (is_numeric($id))
		$data = $this->articleService->getArticle($id);
	    elseif (is_string($id))
		$data = $this->articleService->getArticleAlias($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	$this->setEntity($data);
	$this->template->data = $data;
    }
    
    
    
    public function addComment(ArrayHash $values) {
	try {
	    $comment = new ArticleComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setAuthor($comment->getEditor());
	    $comment->setCreated(new DateTime());
	    $comment->setUpdated(new DateTime());
	    $this->articleService->createComment($comment, $this->getEntity());	    
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
	    $comment = new ArticleComment((array) $values);
	    $comment->setEditor($this->getUser()->getIdentity());
	    $comment->setUpdated(new DateTime());
	    $this->articleService->updateComment($comment, $this->getEntity());	    
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
	    $this->articleService->deleteComment($comm, $this->getEntity());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($comm->getId(), "this", $ex);
	}
	
	if ($this->isAjax()) {
	    $this->redrawControl("commentsData");
	} else {
	    $this->redirect("this");
	}
    }
    
}
