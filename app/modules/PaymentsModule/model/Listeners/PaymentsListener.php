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

namespace App\PaymentsModule\Model\Listeners;

use \Nette\Object,
    \Kdyby\Events\Subscriber,
    \App\Model\Entities\Payment,
    \Kdyby\Monolog\Logger,
    \App\SystemModule\Model\Service\INotificationService;
	
/**
 * PaymentsListener 
 * designated for notify owners of created payments
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class PaymentsListener extends Object implements Subscriber {
    
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
	return ["App\PaymentsModule\Model\Service\PaymentService::onCreate"];
    }
    
    public function onCreate(Payment $p) {
	$this->notifService->notifyNewPayment($p);
	$this->logger->addInfo("System Module - Payments Listener - onCreate - owner of $p has been notified");
    }

}
