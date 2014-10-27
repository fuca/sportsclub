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
    \App\Model\Entities\Position,
    \App\Model\Entities\Role,
    \App\Model\Entities\SportGroup,
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\Services\Exceptions\DataErrorException,
    \App\Model\Misc\Exceptions,
    \Nette\InvalidArgumentException,
    \Nette\DateTime,
    \Grido\DataSources\Doctrine,
    \Doctrine\Common\Collections\ArrayCollection,
    \Nette\Caching\Cache,
    \Nette\Utils\Strings,
    \Kdyby\Monolog\Logger,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Service\BaseService,
    \App\UsersModule\Model\Service\IUserService,
    \App\Model\Service\IRoleService,
    \App\SystemModule\Model\Service\ISportGroupService;

/**
 * Implementation of position service
 *
 * @author <michal.fuca.fucik(at)gmail.com>.
 */
class PositionService extends BaseService implements IPositionService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $positionDao;

    /**
     * @var \App\Model\Service\IRoleService
     */
    private $roleService;

    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;

    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;

    public function setRoleService(IRoleService $roleService) {
	$this->roleService = $roleService;
    }

    public function setSportGroupService(ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }

    public function setUserService(IUserService $us) {
	$this->userService = $us;
    }

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, Position::getClassName(), $logger);
	$this->positionDao = $em->getDao(Position::getClassName());
    }

    public function createPosition(Position $p) {
	if ($p === null)
	    throw new Exceptions\NullPointerException("Argument Position cannot be null");
	try {
	    $this->posGroupTypeHandle($p);
	    $this->posOwnerTypeHandle($p);
	    $this->posRoleTypeHandle($p);
	    $this->positionDao->save($p);
	    $this->invalidateEntityCache($p);
	    
	} catch (DuplicateEntryException $e) {
	    $this->logWarning($e);
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getPosition($id) {
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given");
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);

	    if ($data === null) {
		$data = $this->positionDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id, self::ENTITY_COLLECTION]];
		$cache->save($id, $data, $opt);
	    }
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	return $data;
    }

    private function posGroupTypeHandle(Position $p) {
	if ($p === null)
	    throw new Exceptions\NullPointerException("Argument Position was null");
	try {
	    $sgDb = $this->sportGroupService->getSportGroup($p->getGroup(), false);
	    if ($sgDb !== null) {
		$p->setGroup($sgDb);
	    }
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	return $p;
    }

    private function posOwnerTypeHandle(Position $p) {
	if ($p === null)
	    throw new Exceptions\NullPointerException("Argument Position cannot be null");
	try {
	    $oDb = $this->userService->getUser($p->getOwner(), false);
	    if ($oDb !== null) {
		$p->setOwner($oDb);
	    }
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	return $p;
    }

    private function posRoleTypeHandle(Position $p) {
	if ($p === null)
	    throw new Exceptions\NullPointerException("Argument Position cannot be null");
	try {
	    $rDb = $this->roleService->getRole($p->getRole(), false);
	    if ($rDb !== null) {
		$p->setRole($rDb);
	    }
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function deletePosition(Position $p) {
	if ($p == null)
	    throw new Exceptions\NullPointerException("Argument Position cannot be null");
	try {
	    $db = $this->positionDao->find($p->id);
	    if ($db !== null) {
		$this->positionDao->delete($db);
	    }
	    $this->invalidateEntityCache($p);
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getUserPositions(User $user, $useCache = true) {
	if ($user === null)
	    throw new NullPointerException("Argument User cannot be null");
	try {
	    $id = User::getClassName()."-".$user->getId();
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if (!$useCache) {
		return $this->positionDao->findBy(array("owner" => $user->getId()));
	    }
	    if ($data == null) {
		$data = $this->positionDao->findBy(array("owner" => $user->getId()));
		$opts = [Cache::TAGS => [self::ENTITY_COLLECTION, $id],
			Cache::SLIDING => true];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException(
		    $e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function updatePosition(Position $p) {
	if ($p === null)
	    throw new Exceptions\NullPointerException("Argument Position cannot be null");
	try {
	    $this->entityManager->beginTransaction();
	    $pDb = $this->positionDao->find($p->getId());
	    if ($pDb !== null) {
		$pDb->fromArray($p->toArray());
		$this->posGroupTypeHandle($pDb);
		$this->posOwnerTypeHandle($pDb);
		$this->posRoleTypeHandle($pDb);
		$this->entityManager->merge($pDb);
		$this->entityManager->flush();
	    }
	    $this->entityManager->commit();
	    $this->invalidateEntityCache($pDb);
	} catch (DuplicateEntryException $e) {
	    $this->entityManager->rollback();
	    $this->logError($e);
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getPositionsDatasource() {
	$model = new Doctrine(
		$this->positionDao->createQueryBuilder('pos'));
	return $model;
    }
    
    public function getPositionsWithinGroup($gid, $useCache = true) {
	if (empty($gid) && !is_numeric($gid)) 
	    throw new Exceptions\InvalidArgumentException("Argument gid was bad shaped, '{$gid}' given");
	try {
	    $qb = $this->positionDao->createQueryBuilder();
	    $sg = $this->sportGroupService->getSportGroup($gid, false);
	    $qb->select("p")
		    ->from("App\Model\Entities\Position","p")->where("p.group = :group")
		    ->setParameter("group", $sg);
	    $q = $qb->getQuery();
	    if (!$useCache) {
		return $q->getResult();
	    }
	    $id = User::getClassName()."in".SportGroup::getClassName()."-".$gid;
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data == null) {
		$data = $q->getResult();
		$opts = [Cache::TAGS => [self::ENTITY_COLLECTION, self::STRANGER_COLLECTION,$id],
			Cache::SLIDING => true];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $e) {
	    $this->logError($e);
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }
    
//        public function getPositionsOfGroup(SportGroup $g) {
//	if ($g === null) 
//	    throw new Exceptions\NullPointerException("Argument SportGroup was null");
//	try {
//	    return $this->positionDao->findAll(["group"=>$g->getId()]);
//	} catch (\Exception $e) {
//	    $this->logError($e);
//	    throw new Exceptions\DataErrorException(
//		    $e->getMessage(), $e->getCode(), $e->getPrevious());
//	}
//    }
}
