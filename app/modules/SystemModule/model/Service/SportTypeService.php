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

use App\Model\Entities\SportType,
    \App\Services\Exceptions\DataErrorException,
    App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \App\Services\Exceptions,
    Nette\Caching\Cache,
    \Kdyby\Doctrine\DuplicateEntryException;

/**
 * Service for managing system resources
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class SportTypeService extends BaseService implements ISportTypeService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $sportTypeDao;

    public function __construct(EntityManager $em) {
	parent::__construct($em, SportType::getClassName());
	$this->sportTypeDao = $em->getDao(SportType::getClassName());
    }

    
    // <editor-fold desc="Administration of SPORT TYPES">
    public function createSportType(SportType $type) {
	if ($type == null)
	    throw new NullPointerException("Argument SportType cannot be null", 0);
	try {
	    $this->sportTypeDao->save($type);
	    $this->invalidateEntityCache($type);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function deleteSportType($id) {
	if ($id == null)
	    throw new NullPointerException("Argument SportType cannot be null", 0);
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric", 1);
	try {
	    $db = $this->sportTypeDao->find($id);
	    if ($db !== null) {
		$this->sportTypeDao->delete($db);
	    }
	    $this->invalidateEntityCache($db);
	} catch (\Kdyby\Doctrine\DBALException $ex) {
	    throw new DataErrorException($ex->getMessage(), 1000, $ex);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function getSportType($id, $useCache = true) {
	if ($id === null)
	    throw new NullPointerException("Argument id cannot be null", 0);
	if (!is_numeric($id))
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, $id given", 1);
	try {
	    if (!$useCache) {
		return $this->sportTypeDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);

	    if (empty($data)) {
		$data = $this->sportTypeDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $data;
    }

    public function getSportTypeDataSource() {
	$model = new \Grido\DataSources\Doctrine(
		$this->sportTypeDao->createQueryBuilder("type"));
	return $model;
    }

    public function updateSportType(SportType $type) {
	if ($type === null)
	    throw new NullPointerException("Argument SportType cannot be null", 0);

	try {
	    $dbType = $this->sportTypeDao->find($type->getId());
	    if ($dbType !== null) {
		$dbType->fromArray($type->toArray());
		$this->getEntityManager()->merge($dbType);
		$this->getEntityManager()->flush();
		$this->invalidateEntityCache($dbType);
	    }
	} catch (DuplicateEntryException $ex) {
	    throw new Exceptions\DuplicateEntryException($ex);
	    // TODO LOG?
	} catch (Exception $ex) {
	    // TODO LOG?
	    throw new DataErrorException($ex);
	}
    }

    public function getSelectSportTypes() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	try {
	    if ($data === null) {
		$data = $this->sportTypeDao->findPairs([], "name");
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    return $data;
	} catch (\Exception $e) {
	    // TODO LOG
	    dd($e);
	}
    }
    // </editor-fold>
}
