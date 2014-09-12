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

namespace App\SecurityModule\Model;

use Nette\Security\Permission,
    App\Model\Service\IRoleService,
    App\Model\Service\IAclRuleService,
    App\SecurityModule\Model\Service\IResourceService,
    \App\Model\Misc\Exceptions;

/**
 * Description of dynamic access control list
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class Acl extends Permission {

    /**
     * @var App\Model\Service\IRoleService 
     */
    private $rolesService;

    /**
     * @var App\Model\Service\IAclRuleService 
     */
    private $rulesService;

    /**
     * @var App\SecurityModule\Model\Service\IResourceService
     */
    private $resourcesService;

    public function __construct(IAclRuleService $rulesService, IRoleService $rolesService, IResourceService $resourcesService) {
	$this->rolesService = $rolesService;
	$this->rulesService = $rulesService;
	$this->resourcesService = $resourcesService;
	$this->setRoles();
	$this->setResources();
    }

    private function setRoles() {
	try {
	    $roles = $this->rolesService->getRoles();
	    foreach ($roles as $r) {
		if ($r->getParents()->isEmpty()) {
		    $this->addRole($r->getName(), []);
		} else {
		    $this->addRole($r->getName(), $r->extractParentNames());
		}
	    }
	} catch (Exceptions\DataErrorException $e) {
	    dd($e);
	    // TODO co s tím? LOG a nic víc? jak upozornit uživatele,
	    // že nemůže být přihlášen?
	}
    }

    private function setResources() {
	$resources = $this->resourcesService->getResources();
	foreach ($resources as $res) {
	    if ($res->hasParent()) {
		$this->addResource($res->getId(), $res->getParent());
	    } else {
		$this->addResource($res->getId());
	    }
	}
    }

    private function setRules() {

//	foreach ($privileges as $pv) {
//	    if ($pv[self::PRIVILEGE_MODE_ID] == 1) {
//		if ($pv[self::PRIVILEGE_RESOURCE_ID] == NULL) {
//		    $this->allow($roles[$pv[self::PRIVILEGE_ROLE_ID]]);
//		//dump($roles[$pv[self::PRIVILEGE_ROLE_ID]]);
//		}
//		else
//		    $this->allow($roles[$pv[self::PRIVILEGE_ROLE_ID]][self::ROLE_NAME_ID], 
//			    $resources[$pv[self::PRIVILEGE_RESOURCE_ID]][self::RESOURCE_LINK_ID], 
//			    $pv[self::PRIVILEGE_PRIV_ID]);
//		//dump($roles[$pv[self::PRIVILEGE_ROLE_ID]][self::ROLE_NAME_ID].', '.$resources[$pv[self::PRIVILEGE_RESOURCE_ID]][self::RESOURCE_LINK_ID]);
//	    } else {
//		if ($pv[self::PRIVILEGE_RESOURCE_ID] == NULL) {
//		    $this->deny($roles[$pv[self::PRIVILEGE_ROLE_ID]]);
//		//dump('deny '.$roles[$pv[self::PRIVILEGE_ROLE_ID]]);
//		}
//		else
//		    $this->deny($roles[$pv[self::PRIVILEGE_ROLE_ID]][self::ROLE_NAME_ID], 
//			    $resources[$pv[self::PRIVILEGE_RESOURCE_ID]][self::RESOURCE_LINK_ID], 
//			    $pv[self::PRIVILEGE_PRIV_ID]);
//		//dump('deny '.$roles[$pv[self::PRIVILEGE_ROLE_ID]][self::ROLE_NAME_ID].', '. $resources[$pv[self::PRIVILEGE_RESOURCE_ID]][self::RESOURCE_LINK_ID]);
//	    }
//	}
    }

}
