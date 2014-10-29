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
    \App\SystemModule\Model\Service\Menu\IPublicMenuControlFactory;

/**
 * PublicMenuControlFactory
 * 
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class PublicMenuControlFactory extends BaseService implements IPublicMenuControlFactory {
    
    private $items;
    
    public function getItems() {
	return $this->items;
    }
    
    public function addItem($item) {
	$this->items += $item;
    }
    
    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, "App\SystemModule\Model\Service\Menu\PublicMenuControlFactory", $logger);
	$this->items = new ArrayCollection();
    }
   
}