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

namespace App\UsersModule\Model\Listeners;

use \Nette\Object,
    \Kdyby\Events\Subscriber,
    \Kdyby\Monolog\Logger,
    \App\Model\Entities\User,
    \App\SystemModule\Model\Service\INotificationService;
	
/**
 * UserListener designated for notify user 
 * related with operations executed within user service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class UsersListener extends Object implements Subscriber {
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;

    /**
     * @var \App\SystemModule\Model\Service\INotificationService
     */
    private $notifService;
    
    public function __construct(Logger $logger, INotificationService $notifService) {
	$this->logger = $logger;
	$this->notifService = $notifService;
    }
    
    public function getSubscribedEvents() {
	return ["App\UsersModule\Model\Service\UserService::onCreate",
		"App\UsersModule\Model\Service\UserService::onActivate",
		"App\UsersModule\Model\Service\UserService::onDeactivate",
		"App\UsersModule\Model\Service\UserService::onPasswordRegenerate"];
    }
    
    public function onCreate(User $u) {
	$this->notifService->notifyNewAccount($u);
	$this->logger->addInfo("User Listener - onCreated - user $u notified");
    }
    
    public function onActivate(User $u) {
	$this->notifService->notifyAccountActivated($u);
	$this->logger->addInfo("User Listener - onActivated - user $u notified");
    }
    
    public function onDeactivate(User $u) {
	$this->notifService->notifyAccountDeactivated($u);
	$this->logger->addInfo("User Listener - onDeactivate - user $u notified");
    }
    
    public function onPasswordRegenerate(User $u) {
	$this->notifService->notifyNewPassword($u);
	$this->logger->addInfo("User Listener - onPasswordRegenerate - user $u notified");
	$this->logger->addDebug("User Listener - onPasswordRegenerate - new password for user $u is $u->provideRawPassword()");
    }
}
