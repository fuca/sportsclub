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

use \App\Model\Entities\SportType,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Misc\Exceptions,
    \Kdyby\Doctrine\DBALException,
    \Nette\Caching\Cache,
    \Kdyby\Monolog\Logger,
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

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, SportType::getClassName(), $logger);
	$this->sportTypeDao = $em->getDao(SportType::getClassName());
    }

    
    // <editor-fold desc="Administration of SPORT TYPES">
    
    public function createSportType(SportType $type) {
	if ($type == null)
	    throw new Exceptions\NullPointerException("Argument SportType cannot be null");
	try {
	    $this->sportTypeDao->save($type);
	    $this->invalidateEntityCache($type);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteSportType($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    $db = $this->sportTypeDao->find($id);
	    if ($db !== null) {
		$this->sportTypeDao->delete($db);
	    }
	    $this->invalidateEntityCache($db);
	} catch (DBALException $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DependencyException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSportType($id, $useCache = true) {
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
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
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
	    throw new Exceptions\NullPointerException("Argument SportType cannot be null");
	try {
	    $dbType = $this->sportTypeDao->find($type->getId());
	    if ($dbType !== null) {
		$dbType->fromArray($type->toArray());
		$this->entityManager->merge($dbType);
		$this->entityManager->flush();
		$this->invalidateEntityCache($dbType);
	    }
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex->getMessage());
	    throw new Exceptions\DuplicateEntryException($ex);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSelectSportTypes($useCache = true) {
	try {
	    if (!$useCache) {
		return $data = $this->sportTypeDao->findPairs([], "name");
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load(self::SELECT_COLLECTION);
	    if ($data === null) {
		$data = $this->sportTypeDao->findPairs([], "name");
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    // </editor-fold>
}
