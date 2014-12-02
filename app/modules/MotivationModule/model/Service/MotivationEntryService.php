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
namespace App\MotivationModule\Model\Service;

use \Kdyby\Doctrine\EntityDao,
    \Kdyby\Doctrine\EntityManager,
    \App\MotivationModule\Model\Service\IMotivationEntryService,
    \Nette\Utils\DateTime,
    \Grido\DataSources\Doctrine,
    \Kdyby\GeneratedProxy\__CG__\App\Model\Entities,
    \Kdyby\Monolog\Logger,
    \App\SeasonsModule\Model\Service\ISeasonService,
    \App\UsersModule\Model\Service\IUserService,
    \App\Model\Entities\MotivationEntry,
    \Nette\Caching\Cache,
    \App\Model\Service\BaseService,
    \App\Model\Misc\Exceptions;
/**
 * MotivationEntryService
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class MotivationEntryService extends BaseService implements IMotivationEntryService {
    
    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $entryDao;

    /**
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    private $seasonService;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    /** @var Event dispatched every time after create of MotivationEntry */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of MotivationEntry */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of MotivationEntry */
    public $onDelete = []; 

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, MotivationEntry::getClassName(), $logger);
	$this->entryDao = $em->getDao(MotivationEntry::getClassName());
    }
    
    public function setSeasonService(ISeasonService $seasonService) {
	$this->seasonService = $seasonService;
    }

    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }
        
    public function createEntry(MotivationEntry $e) {
	try {
	    $e->setUpdated(new DateTime());
	    $this->entryEditorTypeHandle($e);
	    $this->entryOwnerTypeHandle($e);
	    $this->entrySeasonTypeHandle($e);
	    $this->entryDao->save($e);
	    $this->invalidateEntityCache($e);
	    $this->onCreate($e);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex->getMessage());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function entrySeasonTypeHandle(MotivationEntry $t) {
	if ($t == null)
	    throw new Exceptions\NullPointerException("Argument MotivationTax cannot be null");
	try {
	    $season = $this->getMixId($t->getSeason());
	    $sportGroup = $this->seasonService->getSeason($season, false);
	    $t->setSeason($sportGroup);
	    return $t;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function entryOwnerTypeHandle(MotivationEntry $p) {
	try {
	    $oId = $this->getMixId($p->getOwner());
	    if ($oId !== null) {
		$owner = $this->userService->getUser($oId, false);
		$p->setOwner($owner);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $p;
    }
    
    private function entryEditorTypeHandle(MotivationEntry $t) {
	if ($t == null)
	    throw new Exceptions\NullPointerException("Argument MotivationTax cannot be null");
	try {
	    $u = $this->getMixId($t->getEditor());
	    if ($u !== null) {
		$editor = $this->userService->getUser($u, false);
		$t->setEditor($editor);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $t;
    }

    public function deleteEntry($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	    $db = $this->entryDao->find($id);
	    if ($db !== null) {
		$this->invalidateEntityCache($db);
		$this->entryDao->delete($db);
		$this->onDelete($db);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getEntriesDataSource(Entities\User $u = null) {
	$qb = $this->entryDao->createQueryBuilder("e");
	if ($u === null) {
	    $model = $qb;
	} else {
	    $model = $qb->where("e.owner = :owner")->setParameter("owner", $u);
	}
	return new Doctrine($model);
    }

    public function getEntry($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->entryDao->find($id);
		$opts = [Cache::TAGS=>[self::ENTITY_COLLECTION, $id, self::SELECT_COLLECTION]];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateEntry(MotivationEntry $e) {
	try {
	    $db = $this->entryDao->find($e->getId());
	    if ($db !== null) {
		$db->fromArray($e->toArray());
		$db->setUpdated(new DateTime());
		$this->entryEditorTypeHandle($db);
		$this->entryOwnerTypeHandle($db);
		$this->entrySeasonTypeHandle($db);
		
		$this->entityManager->merge($db);
		$this->entityManager->flush();
		$this->invalidateEntityCache($db);
		$this->onUpdate($db);
	    }
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex->getMessage());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

}
