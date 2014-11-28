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

namespace App\SystemModule\Model\Listeners;

use \Nette\Object,
    \Kdyby\Events\Subscriber,
    \Kdyby\Monolog\Logger,
    \App\Model\Entities\SportType,
    \App\SystemModule\Model\Service\Menu\IPublicMenuControlFactory;
	
	
/**
 * Description of SportTypeListener
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class SportTypeListener extends Object implements Subscriber {
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;
    
    /**
     * @var \App\SystemModule\Model\Service\Menu\IPublicMenuControlFactory 
     */
    private $publicMenuFactory;
    
    
    function setPublicMenuFactory(IPublicMenuControlFactory $publicMenuFactory) {
	$this->publicMenuFactory = $publicMenuFactory;
    }
        
    public function getSubscribedEvents() {
	return ["App\SystemModule\Model\Service\SportTypeService::onCreate",
		"App\SystemModule\Model\Service\SportTypeService::onUpdate",
		"App\SystemModule\Model\Service\SportTypeService::onDelete"];
    }
    
    public function __construct(Logger $logger) {
	$this->logger = $logger;
    }
    
    public function onCreate(SportType $t) {
	$this->publicMenuFactory->invalidateCache();
    }
    
    public function onUpdate(SportType $t) {
	$this->publicMenuFactory->invalidateCache();
    }
    public function onDelete(SportType $t) {
	$this->publicMenuFactory->invalidateCache();
    }

}