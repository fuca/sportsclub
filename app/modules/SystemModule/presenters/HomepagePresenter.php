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

namespace App\SystemModule\Presenters;
use \App\SystemModule\Presenters\BasePresenter,
    \App\Model\Entities\SportGroup,
    \App\Model\Entities\StaticPage,
    \App\Model\Misc\Exceptions,
    \App\SystemModule\Components\ContactControl,
    \App\ArticlesModule\Model\Service\IArticleService;

/**
 * PublicPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class HomepagePresenter extends SystemPublicPresenter {
    
    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;
    
    /**
     * @inject
     * @var \App\ArticlesModule\Model\Service\IArticleService
     */
    public $articleService;
    
    /**
     * @inject
     * @var \App\SystemModule\Model\Service\IStaticPageService
     */
    public $staticPageService;
    
     /**
     * @inject
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    public $sportGroupService;
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $pagesCollection;
    
    public function getPagesCollection() {
	if (!isset($this->pagesCollection)) {
	    $entity= $this->getEntity();
	    if ($entity !== null) {
		$group = null;
		if ($entity instanceof SportGroup)
		    $group = $entity;
		if ($entity instanceof StaticPage)
		    $group =$entity->getGroup();
		$this->pagesCollection = $this->staticPageService
			    ->getGroupStaticPages($group);
	    }
	}
	return $this->pagesCollection;
    }
    
    public function renderDefault() {
	$this->template->articles = $this->articleService->getArticles();
	$this->template->highlights = $this->articleService->getHighLights();
    }
    
//    private $testValue = 1;
//    
//    public function actionTest() {
//	$this->template->test = $this->testValue;
//    }
//    
//    public function handleChangeTest() {
//	if ($this->isAjax()) {
//	    $this->testValue++;
//	    $this->redrawControl("test");
//	}
//    }
    
    public function actionShowStaticPage($id) {
	$page = null;
	try {
	    if (is_numeric($id)) {
		$page = $this->staticPageService->getStaticPage($id);
	    } else {
		$page = $this->staticPageService->getStaticPageAbbr($id);	
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	$this->setEntity($page);
	$this->template->page = $page;
    }
    
    public function actionShowGroupPages($id) {
	$group = null;
	try {
	    if (is_numeric($id)) {
		$group = $this->sportGroupService->getSportGroup($id);
	    } else {
		$group = $this->sportGroupService->getSportGroupAbbr($id);
	    }
	    $this->setEntity($group);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "default", $ex);
	}
	
	$this->template->pages = $this->getPagesCollection();
	$this->template->group = $group;
    }
    
    /**
     * StaticPageMenu component factory
     * Created only on demand of actionShowGroupPages template
     * @param string $name
     * @return \App\Components\MenuControl
     */
    protected function createComponentStaticPagesMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.publicPages.pagesMenuLabel");
	$id = $this->getParameter("id");
	foreach ($this->getPagesCollection() as $page) {
	    $node = $c->addNode($page->getTitle(), "showStaticPage", true, ["param"=>$page->getAbbr()]);
	    
	    if ($page->getAbbr() == $id)
		$c->setCurrentNode($node);
	}
	return $c;
    }
    
    protected function createComponentStaticPagesGroupsMenu($name) {
	$c = new \App\Components\MenuControl($this, $name);
	$c->setLabel("systemModule.publicPages.groupsMenu");
	$groups = $this->sportGroupService->getAllSportGroups(null, true);
	
	$currentAbbr = null;
	$e = $this->getEntity();
	if ($this->getEntity() instanceof SportGroup) {
	    $currentAbbr = $e->getAbbr();
	} elseif ($e instanceof StaticPage) {
	   $currentAbbr = $e->getGroup()->getAbbr();
	}
	
	foreach ($groups as $g) {
	    $abbr = $g->getAbbr();
	    //if (!$g->getStaticPages()->isEmpty()) {
		$node = $c->addNode($g->getName(), "showGroupPages", true, ["param"=>$abbr]);
		if ($currentAbbr == $abbr) {
		    $c->setCurrentNode($node);
		}
	    //}
	}
	return $c;
    }
    
    public function actionContacts() {
	
	// jak dostat do controlu ICO a DIC?
	// 
	// udelat contact page
	// 
	// vypsat kontakty u skupin z positions (udelat menu skupin)
	// asi mit na vyber kterou skupinu otevrit
	// a sekretare s opet obsahem quick contactu - tohle bude na kazde strance prezentovane jako kontakt na klub
	// KONTAKTY UZ MA NACRTLE TADY AKCE V PRESENTERU
	// my to jen presuneme do auth public
    }

}