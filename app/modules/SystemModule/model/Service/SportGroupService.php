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

namespace App\SystemModule\Model\Service;

use \App\Model\Entities\SportGroup,
    \App\Model\Entities\SportType,
    \App\Model\Entities\User,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Misc\Exceptions,
    \Kdyby\Monolog\Logger,
    \Nette\Caching\Cache,
    \Kdyby\Doctrine\DuplicateEntryException;

/**
 * Service for managing sport types
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class SportGroupService extends BaseService implements ISportGroupService {
    
    const APPLICABLE_SELECT_COLLECTION = "ApplicableSelectList";

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $groupDao;
    
    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $sportTypeDao;
    
    /**
     * Array of event callbacks
     * @var array Array of callbacks
     */
    public $onCreate = [];
    
    /**
     * Array of event callbacks
     * @var array Array of callbacks
     */
    public $onDelete = [];
    
    /**
     * Array of event callbacks
     * @var array Array of callbacks
     */
    public $onUpdate = [];
    
    private $prioritiesList;

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, SportGroup::getClassName(), $logger);
	$this->groupDao = $em->getDao(SportGroup::getClassName());
	$this->sportTypeDao = $em->getDao(SportType::getClassName());
    }
    
        // TODO zvazit jestli se to vubec vyuzije
    // jestli ne, tak to smazat i z cele sport group logiky
    public function getPriorities() { 
	if (!isset($this->prioritiesList)) {
	    $arr = [];
	    for($i = 1; $i <= self::MAX_PRIORITY; $i++) {
		array_push($arr, $i);
	    }
	    $this->prioritiesList = $arr;
	}
	return $this->prioritiesList;
    }

    // <editor-fold desc="Administration of GROUPS">
    public function createSportGroup(SportGroup $g) {
	if ($g == null)
	    throw new Exceptions\NullPointerException("Argument SportGroup cannot be null");
	try {
	    $this->groupParentHandle($g);
	    $this->groupSportTypeHandle($g);
	    $this->groupDao->save($g);
	    $this->invalidateEntityCache($g);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	$this->onCreate(clone $g);
    }

    private function groupParentHandle(SportGroup $g) {
	if ($g === null)
	    throw new Exceptions\NullPointerException("Argument SportType was null");
	try {
	    $parId = $g->getParent();
	    if ($parId !== null) {
		$parDb = $this->getSportGroup($parId, false);
		if ($parDb !== null) {
		    $g->setParent($parDb);
		}
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $g;
    }

    private function groupSportTypeHandle(SportGroup $g) {
	if ($g === null)
	    throw new Exceptions\NullPointerException("Argument SportType was null");
	try {
	    
	    $parent = $g->getParent();
	    if ($parent) {
		$parent = $this->groupDao->find($parent);
		if ($parent->getSportType() != null) {
		    $g->setSportType($parent->getSportType());
		    return $g;
		}
	    }
	    $typeId = $g->getSportType();
	    $typeDb = $this->sportTypeDao->find($typeId);
	    if ($typeDb !== null) {
		$g->setSportType($typeDb);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $g;
    }
    
    public function updateSportGroup(SportGroup $g) {
	if ($g == null)
	    throw new Exceptions\NullPointerException("Argument SportGroup cannot be null", 0);
	try {
	    $this->entityManager->beginTransaction();
	    $dbGroup = $this->getSportGroup($g->getId(), false);

	    if ($dbGroup !== null) {
		$dbGroup->fromArray($g->toArray());
		$this->groupParentHandle($dbGroup);
		$this->groupSportTypeHandle($dbGroup);
		$this->entityManager->merge($dbGroup);
		$this->entityManager->flush();
	    }
	    $this->entityManager->commit();
	 
	    $this->invalidateEntityCache($dbGroup);
	    $this->onUpdate(clone $dbGroup);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	
    }

    public function deleteSportGroup($id) {
	if ($id == null)
	    throw new Exceptions\NullPointerException("Argument id cannot be null", 0);
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    $db = $this->groupDao->find($id);
	    if ($db !== null) {
		$this->groupDao->delete($db);
		$this->invalidateEntityCache($db);
		$this->onDelete(clone $db);
	    }
	} catch (\Kdyby\Doctrine\DBALException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DataErrorException($ex->getMessage(), 1000, $ex);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSportGroup($id, $useCache = true) {
	if ($id === null)
	    throw new Exceptions\NullPointerException("Arument id was null", 0);
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, $id given", 1);
	try {
	    if (!$useCache) {
		return $this->groupDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);

	    if (empty($data)) {
		$data = $this->groupDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $data;
    }
    
    public function getSportGroupAbbr($abbr) {
	if (empty($abbr))
	    throw new Exceptions\InvalidArgumentException("Argument abbr was empty");
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load($abbr);
	    if ($data == null) {
		$data = $this->groupDao->createQueryBuilder("g")
			->where("g.abbr = :abbr")->setParameter("abbr", $abbr)
			->getQuery()->getSingleResult();
		$opts = [Cache::TAGS=>[self::ENTITY_COLLECTION, $abbr, self::SELECT_COLLECTION]];
		$cache->save($abbr, $data, $opts);
	    }
	    return $data;
	} catch (\Doctrine\ORM\NoResultException $ex) {
	    throw new Exceptions\NoResultException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSportGroupsDatasource() {
	$model = new \Grido\DataSources\Doctrine(
		$this->groupDao->createQueryBuilder('g'));
	return $model;
    }
    
    public function getSelectApplicableGroups($id = null) {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::APPLICABLE_SELECT_COLLECTION);
	try {
	    if ($data === null) {
		$all = $this->groupDao->findAll();
		$data = [];
		foreach ($all as $g) {
		    if ($g->getChildren()->isEmpty()) {
			$data = $data+[$g->getId()=>$g->getName()." (".$g->getSportType()->getName().")"];
		    }
		}
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION, self::APPLICABLE_SELECT_COLLECTION]];
		$cache->save(self::APPLICABLE_SELECT_COLLECTION, $data, $opt);
	    }
	    if ($id != null) {
		unset($data[$id]);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSelectAllSportGroups($id = null, $active = null) {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	try {
	    if ($data === null) {
		
		$qb = $this->groupDao->createQueryBuilder("g");
		if ($active !== null) {
		    $qb->where("g.active = :act")
			    ->setParameter("act", $active);
		}
		$all = $qb->getQuery()->getResult();
		$data = [];
		foreach ($all as $g) {
		    $type = $g->getSportType();
		    $data = $data+[$g->getId()=>$g->getName().($type!== null?" ({$type->getName()})":"")];
		}
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    if ($id != null) {
		unset($data[$id]);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getAllSportGroups($root = null, $active = null) {
	if ($active !== null && !is_bool($active))
	    throw new Exceptions\InvalidArgumentException("Argument active has to be type of bool, '$active' given");
	try {
	    if ($root !== null) {
		$qb = $this->entityManager->createQueryBuilder();
		$qb = $qb->select("g")
			    ->from("App\Model\Entities\SportGroup", "g")
			    ->where("g.parent = :parent");
		if ($active !== null) {
		    $qb = $qb->andWhere("g.activity = :act")
			->setParameter("act", $active);
		}
		$qb = $qb->orderBy("ASC", "g.priority, g.name")
			    ->setParameter("parent", $root);
		return $qb->getQuery()->getResult();
	    }  
	    return $this->groupDao->findAll();
	} catch (\Exceptions $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    // </editor-fold>
}
