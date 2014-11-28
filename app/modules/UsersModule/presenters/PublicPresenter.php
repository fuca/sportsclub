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

namespace App\UsersModule\Presenters;
use \App\SystemModule\Presenters\BasePresenter,
    \App\Model\Entities\SportGroup,
    \App\Model\Entities\StaticPage,
    \App\Model\Misc\Exceptions,
    \App\SystemModule\Components\ContactControl,
    \App\SystemModule\Presenters\SystemPublicPresenter,
    \App\ArticlesModule\Model\Service\IArticleService,
    \App\Model\Misc\Enum\WebProfileStatus;

/**
 * Users PublicPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class PublicPresenter extends SystemPublicPresenter {
    
    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;
    
    /**
     * @inject
     * @var \App\SecurityModule\Model\Service\IPositionService
     */
    public $positionService;
    
    private $defaultPlayerRoleName = "player";
    
    public function setPlayerRoleName($name) {
	if($name == "") 
	    throw new Exceptions\InvalidArgumentException("Argument name has to be non empty string");
	$this->defaultPlayerRoleName = $name;
    }
    
    public function renderDefault($gid = self::ROOT_GROUP) {
	$data = null;
	try {
	    if (is_numeric($gid)) {
		$group = $this->sportGroupService->getSportGroup($gid);
	    } else {
		$group = $this->sportGroupService->getSportGroupAbbr($gid);
	    }
	    $data = $this->positionService->getPositionsWithinGroup($group);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($gid, "default", $ex);
	}
	if ($group == null) { // nejsou data pro vykresleni te stranky
	    $this->redirect(":System:Homepage:default");
	}
	$filtered = array_filter($data, 
		function($e) {
		    if ($e->getRole()->getName() == $this->defaultPlayerRoleName) 
			return true;
		    return false;
		});
	$this->template->group = $group;
	$this->template->data = $filtered;
	// show some grid to access users web profiles
    }
    
    public function actionShowWebProfile($uid, $gid) {
	if (!is_numeric($uid))
	    $this->redirect("default");
	$user = null;
	$group = null;
	try {
	    if (is_numeric($gid))
		$group = $this->sportGroupService->getSportGroup($gid);
	    else
		$group = $this->sportGroupService->getSportGroupAbbr($gid);
	    
	    $user = $this->userService->getUser($uid);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($uid, "default", $ex);
	}
	if ($group === null || $user === null) {
	    $this->redirect("default");
	}
	$this->setEntity($user);
	$this->template->group = $group;
	
	$this->template->since = $user->getCreated();
	$this->template->name = $user->getName();
	$this->template->surname = $user->getSurname();
	$this->template->nick = $user->getNick();
	$profile = $user->getWebProfile();
	$this->template->publishable = $profile->getStatus() == WebProfileStatus::OK?true:false;
	$this->template->profile = $profile;
    }
    
    protected function createComponentRostersGroupsMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("usersModule.public.rosters.groupsMenu");
	$groups = $this->sportGroupService->getAllSportGroups(null, true);
	
	$gid = $this->getParameter("gid");
	foreach ($groups as $g) {
	    $id = null;
	    if (is_numeric($gid)) {
		$id = $g->getId();
	    } else {
		$id = $g->getAbbr();
	    }
	    $name = $g->getSportType() !== null?"{$g->getName()} ({$g->getSportType()->getName()})": "{$g->getName()}";
	    $node = $c->addNode($name, ":Users:Public:default", null, ["param"=>$id]);
	    if ($id == $gid || (($gid == self::ROOT_GROUP || $gid === null) && $id == self::ROOT_GROUP))
		$c->setCurrentNode($node);
	}
	return $c;
	
    }
    
    protected function createComponentWebProfileMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	
	$group = $data = null;
	$gid = $this->getParameter("gid");
	try {
	    if (is_numeric($gid))
		$group = $this->sportGroupService->getSportGroup($gid);
	    else
		$group = $this->sportGroupService->getSportGroupAbbr($gid);
	    $raw = $this->positionService->getPositionsWithinGroup($group);
	    
	    $data = array_filter($raw,
		    function ($e) {
			if ($e->getRole()->getName() == $this->defaultPlayerRoleName)
			    return true;
			return false;
		    });
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($gid, "default", $ex);
	}
	$c->setLabel("{$group->getName()} ({$group->getSportType()->getName()})");
	$user = $this->getEntity();
	foreach ($data as $p) {
	    $owner = $p->getOwner();
	    $node = $c->addNode("{$owner->getName()} {$owner->getSurName()}", ":Users:Public:showWebProfile");
	    if ($owner->getId() == $user->getId()) 
		$c->setCurrentNode ($node);
	}
	return $c;
    }
}