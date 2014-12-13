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

namespace App\SecurityModule\Model\Service;

use 
    \App\Model\Service\BaseService,
    \Nette\Caching\Cache,
    \App\SecurityModule\Model\Service\IResourceService,
    \Kdyby\Doctrine\EntityManager,
    \Nette\Security\IAuthorizator,
    \App\Model\Service\IRoleService,
    \App\Model\Service\IAclRuleService,
    \Nette\Security\Permission;

/**
 * ResourceService
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AclService extends BaseService implements IAclService, IAuthorizator {
    
    const ENTITY_ID = "permission";
    
    /**
     * @var \App\Model\Service\IRoleService 
     */
    private $rolesService;

    /**
     * @var \App\Model\Service\IAclRuleService 
     */
    private $rulesService;

    /**
     * @var \App\SecurityModule\Model\Service\IResourceService
     */
    private $resourcesService;
    
    public function __construct(EntityManager $em) {
	parent::__construct($em, "\Nette\Security\Permission");
    }
    
    public function setRolesService(IRoleService $rolesService) {
	$this->rolesService = $rolesService;
    }

    public function setRulesService(IAclRuleService $rulesService) {
	$this->rulesService = $rulesService;
    }

    public function setResourcesService(IResourceService $resourcesService) {
	$this->resourcesService = $resourcesService;
    }
    
    public function invalidateCache() {
	$this->invalidateEntityCache(null, [self::ENTITY_ID]);
    }
    
    private function setRoles(Permission $p) {
	try {
	    $roles = $this->rolesService->getRoles();
	    foreach ($roles as $r) {
		if ($r->getParents()->isEmpty()) {
		    $p->addRole($r->getName(), []);
		} else {
		    $p->addRole($r->getName(), $r->extractParentNames());
		}
	    }
	} catch (Exceptions\DataErrorException $e) {
	    $this->logError($e->getMessage());
	}
    }

    private function setResources(Permission $p) {
	try {
	$resources = $this->resourcesService->getResources();
	} catch (Exceptions\DataErrorException $e) {
	    $this->logError($e->getMessage());
	}
	foreach ($resources as $res) {
	    if ($res->hasParent()) {
		$p->addResource($res->getId(), $res->getParent());
	    } else {
		$p->addResource($res->getId());
	    }
	}
    }

    private function setRules(Permission $p) {
	try {
	    $rules = $this->rulesService->getRules();
	} catch (Exceptions\DataErrorException $e) {
	    $this->logError($e->getMessage());
	}
	foreach ($rules as $r) {
	    if ($r->isPermit()) {
		$p->allow(
			$r->getRole()->getName(), 
			$r->hasResource() ? $r->getResource() : Permission::ALL, 
			$r->hasPrivilege() ? $r->getPrivileges() : Permission::ALL);
	    } else {
		$p->deny(
			$r->getRole()->getName(), 
			$r->hasResource() ? $r->getResource() : Permission::ALL, 
			$r->hasPrivilege() ? $r->getPrivileges() : Permission::ALL);
	    }
	}
    }


    // iiiiiiiiiiiiiiiiiiiii IAclService iiiiiiiiiiiiiiiiiiiiiii
    
    public function getAcl() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::ENTITY_ID);
	if ($data == null) {
	    $data = new Permission();
	    $this->setRoles($data);
	    $this->setResources($data);
	    $this->setRules($data);
	    $opt = [Cache::TAGS => [self::ENTITY_ID]];
	    $cache->save(self::ENTITY_ID, $data, $opt);
	}
	return $data;
    }

    // iiiiiiiiiiiiiiiiiiiii IAuthorizator iiiiiiiiiiiiiiiiiiiii
    public function isAllowed($role, $resource, $privilege) {
	return $this->getAcl()->isAllowed($role, $resource, $privilege);
    }
}