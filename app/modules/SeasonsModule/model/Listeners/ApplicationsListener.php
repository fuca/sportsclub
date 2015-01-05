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

namespace App\SeasonsModule\Model\Listeners;

use \Nette\Object,
    \Kdyby\Events\Subscriber,
    \App\Model\Entities\SeasonApplication,
    \Kdyby\Monolog\Logger,
    \App\SystemModule\Model\Service\INotificationService;
	
/**
 * ApplicationsListener 
 * designated for notify owner of created season applications
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ApplicationsListener extends Object implements Subscriber {
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;
    
    /**
     * @var \App\SystemModule\Model\Service\INotificationService
     */
    private $notifService;
    
    public function __construct(Logger $logger, INotificationService $notif) {
	$this->logger = $logger;
	$this->notifService = $notif;
    }
    
     public function getSubscribedEvents() {
	return ["App\SeasonsModule\Model\Service\SeasonApplicationService::onCreate"];
    }
    
    public function onCreate(SeasonApplication $app) {
	$this->notifService->notifyNewSeasonApplication($app);
	$this->logger->addInfo("System Module - Application Listener - onCreated - owner of season application $app has been notified");
    }

}
