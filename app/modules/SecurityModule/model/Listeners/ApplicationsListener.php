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
    \App\Model\Entities\SeasonApplication,
    \Kdyby\Monolog\Logger,
    \App\Model\Entities\Position,
    \App\SecurityModule\Model\Service\IPositionService,
    \App\Model\Service\IRoleService;
	
	
/**
 * Season application events listener
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ApplicationsListener extends Object implements Subscriber {
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;
    
    /**
     * @return \App\SecurityModule\Model\Service\IPositionService
     */
    private $positionService;
    
    /**
     * @param \App\Model\Service\IRoleService
     */
    private $roleService;
    
    private $defaultRoleName;
    private $defaultComment;
    private $deleteOldPosition;
    
    public function setDeleteOldPosition($deleteOldPosition) {
	if (!is_bool($deleteOldPosition))
	    throw new Exceptions\InvalidStateException("Argument has to be type of boolean, '$deleteOldPosition' given");
	$this->deleteOldPosition = $deleteOldPosition;
    }
    
    public function setDefaultRoleName($defaultRoleName) {
	$this->defaultRoleName = $defaultRoleName;
    }

    public function setDefaultComment($defaultComment) {
	$this->defaultComment = $defaultComment;
    }
    
    public function setRoleService(IRoleService $roleService) {
	$this->roleService = $roleService;
    }
    
    public function setPositionService(IPositionService $positionService) {
	$this->positionService = $positionService;
    }
        
    public function getSubscribedEvents() {
	return ["App\SeasonsModule\Model\Service\SeasonApplicationService::onCreate"];
    }
    
    public function __construct(Logger $logger) {
	$this->logger = $logger;
    }
    
    public function onCreate(SeasonApplication $app) {
	$id = $this->defaultRoleName;
	try {
	    if (!is_string($id)) {
		$role = $this->roleService->getRole($id);
	    } else {
		$role = $this->roleService->getRoleName($id);
	    }
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logger->addError("Application listener - onCreate -  role load failed with - ". $ex->getMessage());
	    return;
	}
	
	$pos = new Position();
	$pos->setGroup($app->getSportGroup());
	$pos->setRole($role);
	$pos->setOwner($app->getOwner());
	$pos->setPublishContact(false);
	$pos->setComment($this->defaultComment);
	
	try {
	    $this->positionService->createPosition($pos);
	    if ($this->deleteOldPosition)
		$this->positionService->deletePositionsWithRole($pos->getOwner(), $pos->getRole());
	} catch (Exceptions\DataErrorException $ex) {
	    $this->logger->addError("Application listener - onCreate - savingData failed with - ". $ex->getMessage());
	    return;
	}
    }

}
