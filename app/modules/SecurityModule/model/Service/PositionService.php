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
    Kdyby\Doctrine\DuplicateEntryException,
    \App\Services\Exceptions\DataErrorException,
    \App\Services\Exceptions,
    \Nette\InvalidArgumentException,
    \Nette\DateTime,
    \Grido\DataSources\Doctrine,
    \Doctrine\Common\Collections\ArrayCollection,
    \Nette\Caching\Cache,
    \Nette\Utils\Strings,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Service\BaseService,
    \App\Model\Service\IUserService,
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
     * @var App\Model\Service\IRoleService
     */
    private $roleService;

    /**
     * @var App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;

    /**
     * @var App\Model\Service\IUserService
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

    public function __construct(EntityManager $em) {
	parent::__construct($em, Position::getClassName());
	$this->positionDao = $em->getDao(Position::getClassName());
    }

    public function createPosition(Position $p) {
	if ($p === null)
	    throw new NullPointerException("Argument Position cannot be null", 0);
	try {
	    $this->posGroupTypeHandle($p);
	    $this->posOwnerTypeHandle($p);
	    $this->posRoleTypeHandle($p);
	    //$owner = $this->userService->getUser($p->getOwner(), false);
	    //$p->setOwner($owner);
	    $this->positionDao->save($p);
	    $this->invalidateEntityCache($p);
	} catch (DuplicateEntryException $ex) {
	    throw new Exceptions\DuplicateEntryException($ex);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function getPosition($id) {
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);

	    if ($data === null) {
		$data = $this->positionDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $data;
    }

    private function posGroupTypeHandle(Position $p) {
	if ($p === null)
	    throw new Exceptions\NullPointerException("Argument Position was null", 0);
	try {
	    $sgDb = $this->sportGroupService->getSportGroup($p->getGroup(), false);
	    if ($sgDb !== null) {
		$p->setGroup($sgDb);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    private function posOwnerTypeHandle(Position $p) {
	if ($p === null)
	    throw new NullPointerException("Argument Position cannot be null", 0);
	try {
	    $oDb = $this->userService->getUser($p->getOwner(), false);
	    if ($oDb !== null) {
		$p->setOwner($oDb);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    private function posRoleTypeHandle(Position $p) {
	if ($p === null)
	    throw new NullPointerException("Argument Position cannot be null", 0);
	try {
	    $rDb = $this->roleService->getRole($p->getRole(), false);
	    if ($rDb !== null) {
		$p->setRole($rDb);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function deletePosition(Position $p) {
	if ($p == null)
	    throw new NullPointerException("Argument Position cannot be null", 0);
	try {
	    $db = $this->positionDao->find($p->id);
	    if ($db !== null) {
		$this->positionDao->delete($db);
	    }
	    $this->invalidateEntityCache($p);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function getUserPositions(User $user) {
	if ($user === null)
	    throw new NullPointerException("Argument User cannot be null", 0);
	try {
	    //$c = \Doctrine\Common\Collections\Criteria::create();
	    //$c->where(Criteria::expr()->eq("owner", $user->getId()));
	    
	    //$res = $this->positionDao->matching($c)->getValues();
	    $res = $this->positionDao->findBy(array("owner" => $user->getId()));
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $res;
    }

    public function updatePosition(Position $p) {
	if ($p == null)
	    throw new NullPointerException("Argument Position cannot be null", 0);
	try {
	    $this->getEntityManager()->beginTransaction();
	    $pDb = $this->positionDao->find($p->getId());
	    if ($pDb !== null) {
		$pDb->fromArray($p->toArray());
		$this->posGroupTypeHandle($pDb);
		$this->posOwnerTypeHandle($pDb);
		$this->posRoleTypeHandle($pDb);
		$this->getEntityManager()->merge($pDb);
		$this->getEntityManager()->flush();
	    }
	    $this->getEntityManager()->commit();
	    $this->invalidateEntityCache($pDb);
	} catch (DuplicateEntryException $ex) {
	    throw new Exceptions\DuplicateEntryException($ex);
	}
    }

    public function getPositionsDatasource() {
	$model = new Doctrine(
		$this->positionDao->createQueryBuilder('pos'));
	return $model;
    }
    
    public function getUsersWithinGroup($gid) {
	if ($gid === null) 
	    throw new Exceptions\NullPointerException("Argument SportGroup was null", 0);
	try {
	    $qb = $this->positionDao->createQueryBuilder("p");
	    $sg = $this->sportGroupService->getSportGroup($gid, false);
	    $qb->select("p.owner.id")->from("Position p")->where("group = :group")->setParameter("group",$sg);
	    return $qb->getQuery()->getResult();
	} catch (\Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

}
