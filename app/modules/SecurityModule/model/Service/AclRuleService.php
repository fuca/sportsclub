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

namespace App\Model\Service;

use \App\Model\Entities\AclRule,
    Kdyby\Doctrine\DuplicateEntryException,
    \App\Model\Misc\Exceptions,
    \Nette\DateTime,
    \Doctrine\Common\Collections\ArrayCollection,
    Nette\Caching\Cache,
    \Nette\Utils\Strings,
    App\Model\Service\BaseService,
    App\Model\Service\IRoleService,
    \Kdyby\Doctrine\EntityManager;

/**
 * Authorization and authentication service
 *
 * @author <michal.fuca.fucik(at)gmail.com>.
 */
class AclRuleService extends BaseService implements IAclRuleService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $aclRuleDao;

    /**
     * @var App\Model\Service\IRoleService
     */
    private $roleService;

    public function setRoleService(IRoleService $roleService) {
	if ($roleService === null)
	    throw new Exceptions\NullPointerException("Argument IRoleService was null", 0);
	$this->roleService = $roleService;
    }

    public function __construct(EntityManager $em) {
	parent::__construct($em, AclRule::getClassName());
	$this->aclRuleDao = $em->getDao(\App\Model\Entities\AclRule::getClassName());
    }

    /**
     * Creates datasource for Rule datagrid
     * @return \Grido\DataSources\Doctrine
     */
    public function getRulesDatasource() {
	$model = new \Grido\DataSources\Doctrine(
		$this->aclRuleDao->createQueryBuilder('rule'));
	return $model;
    }

    public function createRule(AclRule $arule) {
	if ($arule == null)
	    throw new Exceptions\NullPointerException("Argument AclRule cannot be null", 0);
	try {
	    // TODO think about that 
	    $roleDb = $this->roleService->getRole($arule->getRole(), false);
	    //$roleDb = $this->getEntityManager()->getDao(\App\Model\Entities\Role::getClassName())->find($arule->getRole());
	    if ($roleDb !== null) {
		$arule->setRole($roleDb);
	    } else {
		throw new Exceptions\DataErrorException("Attribute Role of AclRule does not exist within database,{$arule->getRole()} given.", 2);
	    }
	    $this->aclRuleDao->save($arule);
	    $this->invalidateEntityCache($arule);
	} catch (DuplicateEntryException $e) {
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), 20, $e);
	} catch (\Nette\InvalidArgumentException $e) {
	    throw new Exceptions\InvalidArgumentException($e);
	} catch (\Exception $e) {
	    // TODO LOG ??
	    throw new Exceptions\DataErrorException($e);
	    
	}
    }

    public function getRule($id, $useCache = true) {
	if ($id === null)
	    throw new Exceptions\NullPointerException("Argument id cannot be null", 0);
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument if has to be type of numeric, {$id} given", 1);
	try {
	    if (!$useCache) {
		return $this->aclRuleDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);

	    if (empty($data)) {
		$data = $this->aclRuleDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (Exception $ex) {
	    throw new Exceptions\DataErrorException($ex);
	}
	return $data;
    }

    public function deleteRule($id) {
	if ($id === null)
	    throw new Exceptions\NullPointerException("Argument AclRule cannot be null", 0);
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric", 1);

	try {
	    $db = $this->aclRuleDao->find($id);
	    if ($db !== null) {
		$this->aclRuleDao->delete($db);
	    }
	    $this->invalidateEntityCache($db);
	} catch (Exception $ex) {
	    throw new Exceptions\DataErrorException($ex);
	}
    }

    public function updateRule(AclRule $arule) {
	if ($arule == null)
	    throw new Exceptions\NullPointerException("Argument AclRule cannot be null", 0);

	try {
	    $this->getEntityManager()->beginTransaction();
	    $dbRule = $this->aclRuleDao->find($arule->getId());
	    if ($dbRule !== null) {
		$dbRule->fromArray($arule->toArray());
		$dbRole = $this->roleService->getRole($arule->getRole(), false);
		$dbRule->setRole($dbRole);
		$this->getEntityManager()->merge($dbRule);
		$this->getEntityManager()->flush();
	    }
	    $this->getEntityManager()->commit();
	    $this->invalidateEntityCache($dbRule);
	} catch (DuplicateEntryException $e) {
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), 20, $e);
	} catch (\Exception $e) {
	    // TODO LOG ??
	    throw new Exceptions\DataErrorException($e);
	}
    }

    public function deleteResource(Resource $r) {
	if ($r == null)
	    throw new Exceptions\NullPointerException("Argument Resource cannot be null", 0);
	$db = $this->resourceDao->find($r->id);
	if ($db !== null) {
	    return $this->resourceDao->delete($db);
	}
    }

    public function updateResource(Resource $r) {
	if ($r == null)
	    throw new Exceptions\NullPointerException("Argument Resource cannot be null", 0);
	$this->resourceDao->save($r);
    }

}
