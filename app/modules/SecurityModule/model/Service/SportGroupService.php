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

use App\Model\Entities\SportGroup,
    App\Model\Entities\User,
    \App\Services\Exceptions\DataErrorException,
    App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \App\Services\Exceptions,
    Nette\Caching\Cache,
    \Kdyby\Doctrine\DuplicateEntryException;

/**
 * Service for managing sport types
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class SportGroupService extends BaseService implements ISportGroupService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $groupDao;

    /**
     *
     * @var App\SystemModule\Model\Service\ISportTypeService
     */
    private $sportTypeService;
    
    private $prioritiesList;
    
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
    
    public function setSportTypeService(ISportTypeService $s) {
	if ($s === null)
	    throw new Exceptions\NullPointerException("Argument ISportTypeService was null", 0);
	$this->sportTypeService = $s;
    }

    public function __construct(EntityManager $em) {
	parent::__construct($em, SportGroup::getClassName());
	$this->groupDao = $em->getDao(SportGroup::getClassName());
    }

    // <editor-fold desc="Administration of GROUPS">
    public function createSportGroup(SportGroup $g) {
	if ($g == null)
	    throw new NullPointerException("Argument SportGroup cannot be null", 0);
	try {
	    $this->groupParentHandle($g);
	    $this->groupSportTypeHandle($g);
	    $this->groupDao->save($g);
	    $this->invalidateEntityCache($g);
	} catch (DuplicateEntryException $ex) {
	    throw new Exceptions\DuplicateEntryException();
	} catch (\Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    private function groupParentHandle(SportGroup $g) {
	if ($g === null)
	    throw new Exceptions\NullPointerException("Argument SportType was null", 0);
	try {
	    $parId = $g->getParent();
	    if ($parId !== null) {
		$parDb = $this->getSportGroup($parId, false);
		if ($parDb !== null) {
		    $g->setParent($parDb);
		}
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $g;
    }

    private function groupSportTypeHandle(SportGroup $g) {
	if ($g === null)
	    throw new Exceptions\NullPointerException("Argument SportType was null", 0);
	try {
	    $typeId = $g->getSportType();
	    $typeDb = $this->sportTypeService->getSportType($typeId, false);
	    if ($typeDb !== null) {
		$g->setSportType($typeDb);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $g;
    }
    
    public function updateSportGroup(SportGroup $g) {
	if ($g == null)
	    throw new NullPointerException("Argument SportGroup cannot be null", 0);
	try {
	    $this->getEntityManager()->beginTransaction();
	    $dbGroup = $this->getSportGroup($g->getId(), false);

	    if ($dbGroup !== null) {
		$dbGroup->fromArray($g->toArray());
		$this->groupParentHandle($dbGroup);
		$this->groupSportTypeHandle($dbGroup);
		$this->getEntityManager()->merge($dbGroup);
		$this->getEntityManager()->flush();
	    }
	    $this->getEntityManager()->commit();
	    $this->invalidateEntityCache($dbGroup);
	} catch (DuplicateEntryException $e) {
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), 20, $e);
	} catch (Exception $ex) {
	    // TODO LOG?
	    throw new DataErrorException($ex);
	}
    }

    public function deleteSportGroup($id) {
	if ($id == null)
	    throw new NullPointerException("Argument id cannot be null", 0);
	if (!is_numeric($id))
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    $db = $this->groupDao->find($id);
	    if ($db !== null) {
		$this->groupDao->delete($db);
		$this->invalidateEntityCache($db);
	    }
	} catch (\Kdyby\Doctrine\DBALException $ex) {
	    throw new DataErrorException($ex->getMessage(), 1000, $ex);
	} catch (\Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function getSportGroup($id, $useCache = true) {
	if ($id === null)
	    throw new Exceptions\NullPointerException("Arument id was null", 0);
	if (!is_numeric($id))
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, $id given", 1);
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
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $data;
    }

    public function getSportGroupsDatasource() {
	$model = new \Grido\DataSources\Doctrine(
		$this->groupDao->createQueryBuilder('g'));
	return $model;
    }

    public function getUserGroups(User $user) {
	if ($user == null)
	    throw new NullPointerException("Argument User was null", 0);
	try {
	    $res = $this->groupDao->findBy(array("owner" => $user->id));
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $res;
    }

    public function getSelectSportGroups($id = null) {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	try {
	    if ($data === null) {
		$data = $this->groupDao->findPairs([], 'name');
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    if ($id != null) {
		unset($data[$id]);
	    }
	    return $data;
	} catch (\Exception $e) {
	    // TODO LOG
	    dd($e);
	}
    }
    // </editor-fold>
}
