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
    \App\SystemModule\Model\Service\ISportGroupService,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Misc\Exceptions,
    \Kdyby\Monolog\Logger, 
    \Nette\Caching\Cache,
    \App\Components\MenuControl,
    \App\Components\MenuControl\MenuNode,
    \Kdyby\Translation\Translator,
    \Doctrine\Common\Collections\ArrayCollection,
    \App\SystemModule\Model\Service\Menu\IItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuControlFactory;

/**
 * AdminMenuControlFactory
 * 
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class AdminMenuControlFactory extends BaseService implements IAdminMenuControlFactory {
    
    private $items;
    
    public function getItems() {
	return $this->items;
    }
    
    public function addItem($item) {
	if (!$item instanceof IItemData)
	    throw new Exceptions\InvalidStateException("Argument item has to be type of MenuNode");
	$this->items->add($item);
    }
    
    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, "App\SystemModule\Model\Service\Menu\AdminMenuControlFactory", $logger);
	$this->items = new ArrayCollection();
    }
    
    public function createComponent($pres, $name) {
	$c = new MenuControl($pres, $name);
	$iterator = $this->items->getIterator();
	$iterator->uasort(function ($a, $b) {
	    return ($a->getLabel() < $b->getLabel()) ? -1 : 1;
	});
	$this->items = new ArrayCollection(iterator_to_array($iterator));
	foreach ($this->items as $i) {
	    $c->addNode($i->getLabel(), $i->getUrl(), $i->getMode(), $i->getData(), $i->getName());
	}
	return $c;
    }

}
