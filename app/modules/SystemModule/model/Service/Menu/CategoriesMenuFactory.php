<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\SystemModule\Model\Service\Menu;

use \App\Model\Service\BaseService,
    \App\SystemModule\Model\Service\ISportGroupService,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Misc\Exceptions,
    \Kdyby\Monolog\Logger, 
    \Nette\Caching\Cache,
    \App\Components\MenuControl,
    \Kdyby\Translation\Translator;
/**
 * Description of CategoriesMenuFactory
 *
 * @author fuca
 */
class CategoriesMenuFactory extends BaseService {
    
    const MENU_CONTROL = "menuControl";
    
    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $groupsService;
    
    /**
     * @var \Kdyby\Translation\Translator
     */
    private $translator;
    
    private $groups;
    
    public function getGroups() {
	if (!isset($this->groups)) {
	    try {
		$this->groups = $this->groupsService->getAllSportGroups();
	    } catch(Exceptions\DataErrorException $ex) {
		$this->logError($ex->getMessage);
		throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	    }
	}
	return $this->groups;
    }

    public function setTranslator(Translator $translator) {
	$this->translator = $translator;
    }
            
    public function setSportGroupsService(ISportGroupService $groupsService) {
	$this->groupsService = $groupsService;
    }

    public function __construct(EntityManager $em, Logger $logger = null) {
	parent::__construct($em, "App\SystemModule\Model\Service\Menu\CategoriesMenuControlFactory", $logger);
    }

    public function createComponent($pres, $name) {
//	$cache = $this->getEntityCache();
//	$data = $cache->load(self::MENU_CONTROL);
//	if ($data === null) {
	    $c = new MenuControl($pres, $name);
	    $c->setLabel($this->translator->translate("system.categoryMenu.label"));

	    $gs = $this->getGroups();
	    $tmp = array_filter($gs, 
			function ($e) {
			    if ($e->getParent() == null) return true;
			    return false;
			});
	    $tmp = $tmp[0];
	    $rootNode = $c->addNode($tmp->getName(), 
		    $pres->link($this->linkModuleHelper($pres), $tmp->getAbbr()), 
		    FALSE, array(), $tmp->getAbbr());
	if ($pres->getParam('abbr') === null && 
		$tmp->getAbbr() == $pres::ROOT_GROUP) {
	    $c->setCurrentNode($rootNode);
	}
	$this->iterateChildren($tmp, $rootNode , $pres, $c);
//	    $data = $c;
//	    $opts = [Cache::TAGS=>[self::MENU_CONTROL, self::ENTITY_COLLECTION]];
//	    $cache->save(self::MENU_CONTROL, $data, $opts);
//	}
	return $c;
    }
    
    private function iterateChildren($rootGroup, $rootNode, $pres, $control) {
	$children = $rootGroup->getChildren();
	if ($children->isEmpty()) return;
	$abbrParam = $pres->getParam('abbr');
	foreach ($children as $c) {
		$abbr = $c->getAbbr();
		$name = $c->getName()." ({$c->getSportType()->getName()})";
		$current = false;
		if ($abbrParam === $abbr) {
		    $current = true;
		} else {
		    if ($abbrParam === null && $abbr == $pres::ROOT_GROUP) {
			$current = true;
		    }
		}
		
		$node = $rootNode->addNode($name, $pres->link($this->linkModuleHelper($pres), $abbr), FALSE, array(), $abbr);
		if ($current)
		    $control->setCurrentNode($node);
		$this->iterateChildren($c, $node, $pres, $control);
	}
    }
    
    private function linkModuleHelper($pres) {
	return ":".$pres->getName().":default";
    }
}
