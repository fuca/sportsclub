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

namespace App\SeasonsModule\Model\Service;

use \App\Model\Entities\Season,
    \App\SeasonsModule\Model\Service\ISeasonService,
    \App\Model\Misc\Exceptions,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\UsersModule\Model\Service\IUserService,
    \Grido\DataSources\Doctrine,
    \Nette\DateTime,
    \Kdyby\Monolog\Logger,
    \Nette\Caching\Cache;

/**
 * Service for managing season related entities
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class SeasonService extends BaseService implements ISeasonService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $seasonDao;

    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;

    public function getUserService() {
	return $this->userService;
    }

    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, Season::getClassName(), $logger);
	$this->seasonDao = $em->getDao(Season::getClassName());
    }

    public function createSeason(Season $s) {
	if ($s === null)
	    throw new Exceptions\NullPointerException("Argument Season cannot be null", 0);
	try {
	    if ($s->getCurrent()) {
		$this->setAllSeasonsAsInactive();   
	    }
	    $this->seasonEditorTypeHandle($s);
	    $s->setUpdated(new DateTime());
	    //$s->setApplications([]);
	    $this->seasonDao->save($s);
	    $this->invalidateEntityCache($s);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException(
	    $ex->getMessage(), Exceptions\DuplicateEntryException::SEASON_LABEL, $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException(
		$ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    // upravit 
//    private function seasonApplicationsCollSetup(Role $r) {
//	$parents = $r->getParents();
//	$parentsCollection = new ArrayCollection();
//	if (is_array($parents) && count($parents) > 0) {
//	    foreach ($parents as $parentId) {
//		$parentObject = $this->roleDao->find($parentId);
//		$parentsCollection->add($parentObject);
//	    }
//	}
//	return $parentsCollection;
//    }

    private function seasonEditorTypeHandle(Season $s) {
	if ($s === null)
	    throw new Exceptions\NullPointerException("Argument Season cannot be null");
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($s->getEditor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $s->setEditor($editor);
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException(
	    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteSeason($id) {
	if ($id == null)
	    throw new Exceptions\NullPointerException("Argument id cannot be null");
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument is has to be type of numeric");
	try {
	    $db = $this->seasonDao->find($id);
	    if ($db !== null) {
		$this->seasonDao->delete($db);
		$this->invalidateEntityCache($db);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException(
	    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSeason($id, $useCache = true) {
	if ($id == null)
	    throw new Exceptions\NullPointerException("Argument id cannot be null");
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, $id given");
	try {
	    if (!$useCache) {
		return $this->seasonDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->seasonDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException(
	    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $data;
    }

    public function updateSeason(Season $s) {
	if ($s === null)
	    throw new Exceptions\NullPointerException("Argument Season cannot be null");
	try {
	    $this->entityManager->beginTransaction();
	    $seasonDb = $this->seasonDao->find($s->getId(), false);
	    if ($seasonDb !== null) {
		if ($s->getCurrent()) {
		    $this->setAllSeasonsAsInactive();   
		}
		$seasonDb->fromArray($s->toArray());
		$this->seasonEditorTypeHandle($seasonDb);
		$this->entityManager->merge($seasonDb);
		$this->entityManager->flush();
	    }
	    $this->entityManager->commit();
	    $this->invalidateEntityCache($seasonDb);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException(
	    $ex->getMessage(), Exceptions\DuplicateEntryException::SEASON_LABEL, $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException(
	    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function setSeasonCurrent($id) {
	$this->setAllSeasonsAsInactive();
	$qb = $this->seasonDao->createQueryBuilder();
	try {
	    $qb->update("App\Model\Entities\Season s")
		    ->set("s.current", 1)->where("s.id = :id")
		    ->setParameter("id", $id)
		    ->getQuery()->getResult();
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException(
	    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    private function setAllSeasonsAsInactive() {
	$qb = $this->seasonDao->createQueryBuilder();
	try {
	    $qb->update("App\Model\Entities\Season s")
		    ->set("s.current", 0)
		    ->getQuery()->getResult();
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException(
	    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSelectSeasons() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	try {
	    if ($data === null) {
		$data = $this->seasonDao->findPairs([], 'label');
		$opt = [Cache::TAGS => [self::ENTITY_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException(
	    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSeasonsDataSource() {
	$model = new Doctrine(
		$this->seasonDao->createQueryBuilder('s'));
	return $model;
    }
    
    public function getCurrentSeason() {
	try {
	    return $this->seasonDao->createQueryBuilder("s")
			->where("s.current = true")
			->getQuery()->getSingleResult();
	} catch (\Exception $ex) {
	    $this->logError($ex);
	    throw new Exceptions\DataErrorException(
	    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

}
