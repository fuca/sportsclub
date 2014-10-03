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
    Kdyby\Doctrine\DuplicateEntryException,
    \App\Services\Exceptions\DataErrorException,
    \App\Model\Misc\Exceptions,
    \Nette\InvalidArgumentException,
    \Nette\DateTime,
    \Doctrine\Common\Collections\ArrayCollection,
    \Nette\Caching\Cache,
    \Nette\Utils\Strings,
    \Kdyby\Doctrine\EntityManager,
    App\Model\Service\BaseService,
    Grido\DataSources\Doctrine;

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

    public function __construct(EntityManager $em) {
	parent::__construct($em, Role::getClassName());
	$this->roleDao = $em->getDao(Role::getClassName());
	$this->positionDao = $em->getDao(Position::getClassName());
    }

    public function createRole(Role $r) {
	if ($r == null)
	    throw new NullPointerException("Argument Role cannot be null", 0);
	try {
	    $r->setParents($this->roleParentsCollSetup($r));
	    $r->setAdded(new DateTime());
	    $this->roleDao->save($r);
	    $this->invalidateEntityCache($r);
	} catch (DuplicateEntryException $e) {
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), 20, $e);
	} catch (\Exception $e) {
	    // TODO LOG ??
	    throw new DataErrorException($e);
	}
    }

    private function roleParentsCollSetup(Role $r) {
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
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    if (!$useCache) {
		return $this->roleDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->roleDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (Exception $e) {
	    throw new DataErrorException($e);
	}
	return $data;
    }

    public function getRoles() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::ENTITY_COLLECTION);
	if ($data == null) {
	    $data = $this->roleDao->findAll();
	    $opt = [
		Cache::TAGS => [self::ENTITY_COLLECTION],
		Cache::SLIDING => true];
	    $cache->save(self::ENTITY_COLLECTION, $data, $opt);
	}
	return $data;
    }

    // TODO add cache support
    public function getUserRoles(User $user) {
	if ($user == null)
	    throw new NullPointerException("Argument User cannot be null", 0);
	$res = $this->positionDao->findBy(array("owner" => $user->id));
	return $res;
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

		/* 	TODO	MAKES A CLONE COPY OF ARRAY
		 * 		$ai = new \ArrayIterator();
		  $ai->append($data);
		  $data = $ai->getArrayCopy();
		 */
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
	    // TODO LOG
	    dd($e);
	}
    }

    public function deleteRole($id) {
	if ($id == null)
	    throw new NullPointerException("Argument Role cannot be null", 0);
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric", 1);

	try {
	    $db = $this->roleDao->find($id);
	    if ($db !== null) {
		$this->roleDao->delete($db);
	    }
	    $this->invalidateEntityCache($db);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function updateRole(Role $r) {
	if ($r == null)
	    throw new Exceptions\NullPointerException("Argument Role cannot be null", 0);
	$dbRole = $this->roleDao->find($r->getId());

	if ($dbRole !== null) {
	    $dbRole->fromArray($r->toArray());

	    $dbRole->setParents($this->roleParentsCollSetup($r));
	    try {
		$this->entityManager->merge($dbRole);
		$this->entityManager->flush();
	    } catch (DuplicateEntryException $e) {
		throw new Exceptions\DuplicateEntryException($e->getMessage(), 20, $e);
	    } catch (\Exception $e) {
		// TODO LOG ??
		throw new Exceptions\DataErrorException($e);
	    }
	}
	$this->invalidateEntityCache($r);
    }
}
