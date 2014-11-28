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

namespace App\SecurityModule\Presenters;
use \App\SystemModule\Presenters\BasePresenter,
    \App\Model\Entities\SportGroup,
    \App\Model\Entities\StaticPage,
    \App\Model\Misc\Exceptions,
    \App\SystemModule\Components\ContactControl,
    \App\SystemModule\Presenters\SystemPublicPresenter,
    \App\ArticlesModule\Model\Service\IArticleService;

/**
 * Security module PublicPresenter
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
    
    public function actionDefault($gid = self::ROOT_GROUP) {
	$data = false;
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
	$this->template->group = $group;
	$this->template->data = $data;
    }

    protected function createComponentContactsGroupsMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("securityModule.public.contacts.groupsMenu");
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
	    $node = $c->addNode($name, ":Security:Public:default", null, ["param"=>$id]);
	    if ($id == $gid || (($gid == self::ROOT_GROUP || $gid === null) && $id == self::ROOT_GROUP))
		$c->setCurrentNode($node);
	}
	return $c;
	
    }
}