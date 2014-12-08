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

use \App\SystemModule\Presenters\SystemClubPresenter,
    \App\SecurityModule\Model\Misc\Annotations\Secured,
    \Nette\Utils\ArrayHash,
    \Nette\Utils\DateTime,
    \App\Model\Entities\WallpostComment,
    \App\WallsModule\Components\PermanentWallposts,
    \App\Model\Misc\Exceptions,
    \App\Model\Misc\Enum\WallPostStatus,
    \App\WallsModule\Components\WallHistoryControl;
    

/**
 * WallsProtectedPresenter
 * @Secured(resource="WallsClub"))
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ProtectedPresenter extends SystemClubPresenter {

    /**
     * @inject
     * @var \App\WallsModule\Model\Service\IWallService
     */
    public $wallService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;
    
    /**
     * @Secured(resource="default")
     */
    public function actionDefault($abbr = self::ROOT_GROUP) {
	$sg = null;
	try {
	    if (is_string($abbr))
		$sg = $this->sportGroupService->getSportGroupAbbr($abbr);
	    elseif (is_numeric($abbr))
		$sg = $this->sportGroupService->getSportGroup($abbr);
	    $wps = $this->wallService->getWallPosts($sg, false, WallPostStatus::PUBLISHED);
	    $this->template->wallPosts = $wps;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handledataLoad($abbr, "default", $ex);
	}
	$type = $sg->getSportType();
	if ($type !== null) 
	    $type =  " ({$type->getName()})";
	else 
	    $type = "";
	$this->template->groupLabel = $sg->getName().$type;
	$this->template->abbr = $sg->getAbbr();
	$this->template->group = $sg;
    }

    /**
     * @Secured(resource="showWallpost")
     */
    public function actionShowWallPost($id, $abbr = self::ROOT_GROUP) {
	if (!is_numeric($id))
	    $this->handleBadArgument($id);
	$sg = null;
	$wp = [];
	try {
	    if (is_string($abbr))
		$sg = $this->sportGroupService->getSportGroupAbbr($abbr);
	    elseif (is_numeric($abbr))
		$sg = $this->sportGroupService->getSportGroup($abbr);

	    $wp = $this->wallService->getWallPost($id);
	    if ($wp !== null) {
		$this->setEntity($wp);
	    }

	    $type = $sg->getSportType();
	    if ($type !== null)
		$type = " ({$type->getName()})";
	    else
		$type = "";
	    $this->template->groupLabel = $sg->getName() . $type;
	    $this->template->data = $wp;
	    $this->template->abbr = $abbr;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
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
	    $comment = new WallpostComment((array) $values);
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
	    $this->wallService->deleteComment($comm, $this->getEntity());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataDelete($comm->getId(), "this", $ex);
	}
	
	if ($this->isAjax()) {
	    $this->redrawControl("commentsData");
	} else {
	    $this->redirect("this");
	}
    }
    
    protected function createComponentPermanentWallPosts($name) {
	$c = new PermanentWallposts($this, $name);
	$data = $group = null;
	try {
	    $gid = empty($abbr = $this->getParameter("abbr"))?self::ROOT_GROUP:$abbr;
	    
	    if (is_numeric($gid))
		$group = $this->sportGroupService->getSportGroup($gid);
	    else 
		$group = $this->sportGroupService->getSportGroupAbbr($gid);    
	    
	    $data = $this->wallService->getHighlights($group, self::ROOT_GROUP, true);
//	    $filtered = array_filter($data, 
//		    function ($e) {
//			if ($e->getGroup())
//			    return true;
//			return false;
//	    });
//	    return $filtered;
	} catch (Exceptions\DataErrorException $ex) {
	    $this->presenter->handleDataLoad(null, ":System:Default:clubRoot", $ex);
	}
	$c->setData($data);
	$c->setParam($gid);
	return $c;
    }
    
    
    protected function createComponentWallHistoryControl($name) {
	$c = new WallHistoryControl($this, $name);
	$data = $group = null;
	try {
	    $gid = empty($abbr = $this->getParameter("abbr"))?self::ROOT_GROUP:$abbr;
	    
	    if (is_numeric($gid))
		$group = $this->sportGroupService->getSportGroup($gid);
	    else 
		$group = $this->sportGroupService->getSportGroupAbbr($gid);
	    $data = $this->wallService->getOldWallPosts($group);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->presenter->handleDataLoad(null, ":System:Default:clubRoot", $ex);
	}
	$c->setData($data);
	$c->setParam($gid);
	return $c;
    }

}
