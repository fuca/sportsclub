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

namespace App\SecurityModule\Model\Listeners;

use \Nette\Object,
    \Kdyby\Events\Subscriber,
    \Kdyby\Monolog\Logger,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\SecurityModule\Model\Service\IAclService;

/**
 * Acl rule events listener
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AclRuleListener extends Object implements Subscriber {

    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;

    /**
     * @return \App\SecurityModule\Model\Service\IAclService
     */
    private $aclService;

    public function getSubscribedEvents() {
	return ["App\Model\Service\AclRuleService::onCreate",
	    "App\Model\Service\AclRuleService::onUpdate",
	    "App\Model\Service\AclRuleService::onDelete"];
    }

    public function __construct(Logger $logger, IAclService $aclService) {
	$this->logger = $logger;
	$this->aclService = $aclService;
    }

    /**
     * onCreate event handler
     * @param BaseEntity $e
     */
    public function onCreate(BaseEntity $e) {
	$this->aclService->invalidateCache();
	$this->logger->addInfo("AclRuleListener - onCreate - cache of aclService is gonna be deleted");
    }

    /**
     * onUpdate event handler
     * @param BaseEntity $e
     */
    public function onUpdate(BaseEntity $e) {
	$this->aclService->invalidateCache();
	$this->logger->addInfo("AclRuleListener - onUpdate - cache of aclService is gonna be deleted");
    }

    /**
     * onDelete event handler
     * @param BaseEntity $e
     */
    public function onDelete(BaseEntity $e) {
	$this->aclService->invalidateCache();
	$this->logger->addInfo("AclRuleListener - onDelete - cache of aclService is gonna be deleted");
    }

}
