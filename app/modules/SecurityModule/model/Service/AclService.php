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
	$this->invalidateEntityCache();
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
	    dd($e);
	    // TODO co s tím? LOG a nic víc? jak upozornit uživatele,
	    // že nemůže být přihlášen?
	}
    }

    private function setResources(Permission $p) {
	try {
	$resources = $this->resourcesService->getResources();
	} catch (Exceptions\DataErrorException $e) {
	    dd($e);
	    // TODO co s tím? LOG a nic víc? jak upozornit uživatele,
	    // že nemůže být přihlášen?
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
	// ACL pomoci kompozice hodit do servisy, ktera si to bude cachovat protoze tohle je moc narocny pocitani, tohle nam umozni chytat vyjimky z construktoru pri tomhle vytvareni a muzeme byt schopni v ty service nejak reagovat na to, ze se nepodari sestavit strom ACL
	// 
	// dal ta servisa musi umoznovat mazani kese zvenku, ptz pridani role, pravidla, ji muze ovlivnit
	
	// do administrace acl pravidla je potreba pridat moznost dynamicke editace privileges
	// privileges u aclRule musi byt mnozina a ne jen jedno
	try {
	    $rules = $this->rulesService->getRules();
	} catch (Exceptions\DataErrorException $e) {
	    dd($e);
	    // TODO co s tím? LOG a nic víc? jak upozornit uživatele,
	    // že nemůže být přihlášen?
	}
	foreach ($rules as $r) {
	    if ($r->isPermit()) {
		$p->allow(
			$r->getRole(), 
			$r->hasResource() ? $r->getResource() : Permission::ALL, 
			$r->hasPrivileges() ? $r->getPrivileges() : Permission::ALL);
	    } else {
		$p->deny(
			$r->getRole(), 
			$r->hasResource() ? $r->getResource() : Permission::ALL, 
			$r->hasPrivileges() ? $r->getPrivileges() : Permission::ALL);
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
	    // kdyz zavedu constanty ROLE_COLLECTION atd.. tak muzu dat i taady tomu flag ROLE_COLLECTION a on se to pak smaze samo, to je lepsi jak volat metodu, ikdyz to je jiny namespace, takze nesmaze, takze proto jsem to chtel dat cely modul do jednoho namespace
	    $cache->save(self::ENTITY_ID, $data, $opt);
	}
	return $data;
    }

    // iiiiiiiiiiiiiiiiiiiii IAuthorizator iiiiiiiiiiiiiiiiiiiii
    public function isAllowed($role, $resource, $privilege) {
	return $this->getAcl()->isAllowed($role, $resource, $privilege);
    }
    
    
    // pokud to nebude fungovat, tak se to da zaregistrovat jak jsem si myslel, ale mit to jako zabalenou servisu s prekrytou metodou se mi zda cistejsi
//    services:
//    database:
//        class: Nette\Database\Connection
//        factory: DbFactory::createConnection
}