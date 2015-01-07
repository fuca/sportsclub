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

namespace App\SystemModule\Model\Service\Menu;

use \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Misc\Exceptions,
    \Kdyby\Monolog\Logger, 
    \Nette\Caching\Cache,
    \App\Components\MenuControl,
    \App\Components\MenuControl\MenuNode,
    \Kdyby\Translation\Translator,
    \App\SystemModule\Components\PublicMenuControl,
    \Doctrine\Common\Collections\ArrayCollection,
    \App\SystemModule\Model\Service\Menu\IItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuControlFactory,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\SystemModule\Model\Service\IStaticPageService,
    \App\SystemModule\Model\Service\ISportTypeService,
    \App\SystemModule\Components\PublicMenuControl AS PMC;


/**
 * PublicMenuControlFactory
 * 
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class PublicMenuControlFactory extends BaseService implements IPublicMenuControlFactory {
    
    const PUBLIC_MENU_COLLECTION = "publicMenuCollection";
    
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $items;
    
    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;
    
    /**
     * @var \App\SystemModule\Model\Service\ISportTypeService
     */
    private $sportTypeService;

    function setSportGroupService(ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }
    
    function setSportTypeService(ISportTypeService $sportTypeService) {
	$this->sportTypeService = $sportTypeService;
    }

    public function getItems() {
	return $this->items;
    }
    
    public function addItem($item) {
	if (!$item instanceof IItemData)
	    throw new Exceptions\InvalidStateException("Argument item has to be type of MenuNode");
	$this->items->add($item);
    }
    
    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, $this->getClassName(), $logger);
	$this->items = new ArrayCollection();
    }
    
    public function createComponent($pres, $name) {
	$c = new PublicMenuControl($pres, $name, 
		$this->sportGroupService, 
		$this->sportTypeService);
	
	$iterator = $this->items->getIterator();
	$iterator->uasort(function ($a, $b) {
	    return ($a->getLabel() < $b->getLabel()) ? -1 : 1;
	});
	$this->items = new ArrayCollection(iterator_to_array($iterator));
	
	$c->setBrands($this->items);
	$c->setTreeData($this->getTreeData());
	return $c;
    }
    
    private function getTreeData() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::PUBLIC_MENU_COLLECTION);
	if ($data == null) {
	    $data = $this->prepareTreeData();    
	    $opts = [Cache::TAGS => [self::PUBLIC_MENU_COLLECTION],
		    Cache::SLIDING => true];
	    $cache->save(self::PUBLIC_MENU_COLLECTION, $data, $opts);
	}
	return $data;
    }
    
    private function prepareTreeData() {
	$res = [];
	try {
	    $sportTypes = $this->sportTypeService->getAllSportTypes(true, true);
	    $groups = $this->sportGroupService->getAllSportGroups(null, true);
	    $rootArray = array_filter($groups, 
			function ($e) {
			    if ($e->getParent() == null) return true;
			    return false;
			});
	    if (!empty($rootArray)) {
		$rootGroup = $rootArray[0];
		foreach ($sportTypes as $type) {
		    $typeGroups = array_filter($groups, 
			    function($e) use ($type) {
			if ($e->getSportType() != null && $e->getSportType()->getId() == $type->getId()) 
			    return true;
			return false;
			    });
		    array_push($res, [
			    PMC::TYPE_ID	=> $type, 
			    PMC::GROUPS_ID	=> $typeGroups, 
			    PMC::ROOT_ID	=> $rootGroup]);
		}
	    }
	    
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logError($ex);
	    throw new Exceptions\InvalidStateException(
		    "COMPONENT PublicMenu could not be inicialized - {$ex->getMessage()}", $ex->getCode(), $ex->getPrevious());
	}
	return $res;
    }
    
    public function invalidateCache() {
	 $tags = [
		self::ENTITY_COLLECTION, 
		self::SELECT_COLLECTION,
		self::PUBLIC_MENU_COLLECTION];
	$this->invalidateEntityCache(null, $tags);
    }

}

