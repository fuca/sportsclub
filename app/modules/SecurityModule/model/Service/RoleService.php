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

use \App\Model\Entities\User,
    \App\Model\Entities\Role,
    \App\Model\Entities\Position,
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\Model\Misc\Exceptions,
    \Nette\InvalidArgumentException,
    \Nette\DateTime,
    \Doctrine\Common\Collections\ArrayCollection,
    \Nette\Caching\Cache,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Service\BaseService,
    \Grido\DataSources\Doctrine;

/**
 * Role service implementation
 *
 * @author <michal.fuca.fucik(at)gmail.com>.
 */
class RoleService extends BaseService implements IRoleService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $roleDao;
    
    /**
     * 
     * @param \Kdyby\Doctrine\EntityDao
     */
    private $positionDao;
    
    /** @var Event dispatched every time after create of Role */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of Role */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of Role */
    public $onDelete = [];

    public function __construct(EntityManager $em) {
	parent::__construct($em, Role::getClassName());
	$this->roleDao = $em->getDao(Role::getClassName());
	$this->positionDao = $em->getDao(Position::getClassName());
    }

    public function createRole(Role $r) {
	if ($r === null)
	    throw new Exceptions\NullPointerException("Argument Role cannot be null");
	try {
	    $r->setParents($this->roleParentsCollSetup($r));
	    $r->setAdded(new DateTime());
	    $this->roleDao->save($r);
	    $this->invalidateEntityCache($r);
	    $this->onCreate($r);
	} catch (DuplicateEntryException $e) {
	    $this->logWarning($e);
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    private function roleParentsCollSetup(Role $r) {
	if ($r === null) 
	    throw new Exceptions\NullPointerException("Argument Role cannot be null");
	$parents = $r->getParents();
	$parentsCollection = new ArrayCollection();
	if (is_array($parents) && count($parents) > 0) {
	    foreach ($parents as $parentId) {
		$parentObject = $this->roleDao->find($parentId);
		$parentsCollection->add($parentObject);
	    }
	}
	return $parentsCollection;
    }

    public function getRole($id, $useCache = true) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    if (!$useCache) {
		return $this->roleDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->roleDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id, self::ENTITY_COLLECTION]];
		$cache->save($id, $data, $opt);
	    }
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	return $data;
    }
    
    public function getRoleName($name) {
	if (!is_string($name)) 
	    throw new Exceptions\InvalidArgumentException("Argument name was null");
	try {
	    return $this->roleDao->createQueryBuilder("r")
			->where("r.name = :name")->setParameter("name", $name)
			->getQuery()->getSingleResult();
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getRoles() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::ENTITY_COLLECTION);
	try {
	    if ($data == null) {
		$data = $this->roleDao->findAll();
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

    public function getUserRoles(User $user, $useCache = true) {
	if ($user === null)
	    throw new NullPointerException("Argument User cannot be null");
	try {
	    if (!$useCache) {
		return $this->positionDao->findBy(array("owner" => $user->getId()));
	    }
	    $id = User::getClassName()."-".$user->getId();
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->positionDao->findBy(array("owner" => $user->getId()));
		$opts = [Cache::TAGS => [self::ENTITY_COLLECTION, $id],
			Cache::SLIDING => true];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getRolesDatasource() {
	$model = new Doctrine(
		$this->roleDao->createQueryBuilder('role'));
	return $model;
    }

    public function getSelectRoles($id = null) {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	try {
	    if ($data === null) {
		$data = $this->roleDao->findPairs([], 'name');
		$opt = [Cache::TAGS => [self::ENTITY_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    if ($id != null) {
		if (is_numeric($id)) {
		    unset($data[$id]);
		} else if (is_string($id)) {
		    $data = array_flip($data);
		    unset($data[$id]);
		    $data = array_flip($data);
		}
	    }
	    return $data;
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function deleteRole($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    $db = $this->roleDao->find($id);
	    if ($db !== null) {
		$this->roleDao->delete($db);
		$this->onDelete($db);
	    }
	    $this->invalidateEntityCache($db);
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function updateRole(Role $r) {
	if ($r === null)
	    throw new Exceptions\NullPointerException("Argument Role cannot be null");
	try {
	    $dbRole = $this->roleDao->find($r->getId());
	    if ($dbRole !== null) {
	
		$dbRole->fromArray($r->toArray());
		$dbRole->setParents($this->roleParentsCollSetup($r));
		$this->entityManager->merge($dbRole);
		$this->entityManager->flush();
		$this->invalidateEntityCache($dbRole);
		$this->onUpdate(clone $dbRole);
	    }
	} catch (DuplicateEntryException $e) {
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }
}
