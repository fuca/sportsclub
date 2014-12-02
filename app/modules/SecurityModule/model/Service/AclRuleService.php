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
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\Model\Misc\Exceptions,
    \Nette\DateTime,
    \Doctrine\Common\Collections\ArrayCollection,
    \Nette\Caching\Cache,
    \Kdyby\Monolog\Logger,
    \Grido\DataSources\Doctrine,
    \App\Model\Service\BaseService,
    \App\Model\Service\IRoleService,
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
     * @var \App\Model\Service\IRoleService
     */
    private $roleService;
    
    /** @var Event dispatched every time after create of AclRule */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of AclRule */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of AclRule */
    public $onDelete = [];

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, AclRule::getClassName(), $logger);
	$this->aclRuleDao = $em->getDao(AclRule::getClassName());
    }
    
    public function setRoleService(IRoleService $roleService) {
	if ($roleService === null)
	    throw new Exceptions\NullPointerException("Argument IRoleService was null", 0);
	$this->roleService = $roleService;
    }

    /**
     * Creates datasource for Rule datagrid
     * @return \Grido\DataSources\Doctrine
     */
    public function getRulesDatasource() {
	$model = new Doctrine(
		$this->aclRuleDao->createQueryBuilder('rule'));
	return $model;
    }

    public function createRule(AclRule $arule) {
	if ($arule === null)
	    throw new Exceptions\NullPointerException("Argument AclRule cannot be null");
	try {
	    $this->roleTypeHandle($arule);
	    $this->aclRuleDao->save($arule);
	    $this->invalidateEntityCache($arule);
	    $this->onCreate($arule);
	    
	} catch (DuplicateEntryException $e) {
	    $this->logWarning($e);
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }
    
    private function roleTypeHandle(AclRule $e) {
	if ($e === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null", 0);
	try {
	    $role = null;
	    $id = $this->getMixId($e->getRole());
	    if ($id !== null) $role = $this->roleService->getRole($id, false);
	    $e->setRole($role);
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getRule($id, $useCache = true) {
	if ($id === null)
	    throw new Exceptions\NullPointerException("Argument id cannot be null");
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument if has to be type of numeric, {$id} given");
	try {
	    if (!$useCache) {
		return $this->aclRuleDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);

	    if (empty($data)) {
		$data = $this->aclRuleDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id, self::ENTITY_COLLECTION]];
		$cache->save($id, $data, $opt);
	    }
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	return $data;
    }
    
    public function getRules() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::ENTITY_COLLECTION);
	try {
	    if ($data === null) {
		$data = $this->aclRuleDao->findAll();
		$opt = [
		    Cache::TAGS => [self::ENTITY_COLLECTION],
		    Cache::SLIDING => true];
		$cache->save(self::ENTITY_COLLECTION, $data, $opt);
	    }
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	return $data;
    }

    public function deleteRule($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given");
	try {
	    $db = $this->aclRuleDao->find($id);
	    if ($db !== null) {
		$this->aclRuleDao->delete($db);
		$this->onDelete(clone $db);
	    }
	    $this->invalidateEntityCache($db);
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function updateRule(AclRule $arule) {
	if ($arule === null)
	    throw new Exceptions\NullPointerException("Argument AclRule cannot be null");
	try {
	    $this->entityManager->beginTransaction();
	    $dbRule = $this->aclRuleDao->find($arule->getId());
	    if ($dbRule !== null) {
		$dbRule->fromArray($arule->toArray());
		$this->roleTypeHandle($dbRule);
		$this->entityManager->merge($dbRule);
		$this->entityManager->flush();
		
	    }
	    $this->entityManager->commit();
	    $this->invalidateEntityCache($dbRule);
	    $this->onUpdate($dbRule);
	} catch (DuplicateEntryException $e) {
	    $this->entityManager->rollback();
	    $this->logWarning($e);
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    $this->entityManager->rollback();
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }
}
