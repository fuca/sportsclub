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

use \Kdyby\Doctrine\EntityManager,
    \App\MotivationModule\Model\Service\IMotivationTaxService,
    \Kdyby\Monolog\Logger,
    \Nette\Caching\Cache,
    \Nette\Utils\DateTime,
    \Grido\DataSources\Doctrine,
    \App\Model\Entities\MotivationTax,
    \App\Model\Service\BaseService,
    \App\Model\Misc\Exceptions,
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\UsersModule\Model\Service\IUserService,
    \App\SeasonsModule\Model\Service\ISeasonService;

/**
 * MotivationTaxService
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class MotivationTaxService extends BaseService implements IMotivationTaxService {
    
    /**
     * @var Kdyby\Doctrine\EntityDao
     */
    private $taxDao;
    
    /**
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    private $seasonService;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;
    
    public function setSeasonService(ISeasonService $seasonService) {
	$this->seasonService = $seasonService;
    }

    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }

    public function setSportGroupService(ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }
        
    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, MotivationTax::getClassName(), $logger);
	$this->taxDao = $em->getDao(MotivationTax::getClassName());
    }
    
    public function createTax(MotivationTax $t) {
	try {
	    $t->setUpdated(new DateTime());
	    $t->setOrderedDate(new DateTime());
	    $this->taxEditorTypeHandle($t);
	    $this->taxSeasonTypeHandle($t);
	    $this->taxSportGroupTypeHandle($t);
	    
	    $this->taxDao->save($t);
	    $this->invalidateEntityCache($t);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex->getMessage());
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function taxSeasonTypeHandle(MotivationTax $t) {
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

    private function taxSportGroupTypeHandle(MotivationTax $t) {
	if ($t == null)
	    throw new Exceptions\NullPointerException("Argument MotivationTax cannot be null");
	try {
	    $group = $this->getMixId($t->getSportGroup());
	    $sportGroup = $this->sportGroupService
		    ->getSportGroup((integer) $group, false);
	    $t->setSportGroup($sportGroup);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $t;
    }

    private function taxEditorTypeHandle(MotivationTax $t) {
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
    

    public function deleteTax($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	    $db = $this->taxDao->find($id);
	    if ($db !== null) {
		$this->invalidateEntityCache($db);
		$this->taxDao->delete($db);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getTax($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data == null) {
		$data = $this->taxDao->find($id);
		$opts = [Cache::TAGS=>[self::ENTITY_COLLECTION, $id, self::SELECT_COLLECTION],
		    Cache::SLIDING=>true];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getTaxesDatasource() {
	$model = new Doctrine(
		$this->taxDao->createQueryBuilder("t"));
	return $model;
    }

    public function updateTax(MotivationTax $t) {
	try {
	    $db = $this->taxDao->find($t->getId());
	    if ($db !== null) {
		$db->fromArray($t->toArray());
		$db->setUpdated(new DateTime());
		$this->taxEditorTypeHandle($db);
		$this->taxSeasonTypeHandle($db);
		$this->taxSportGroupTypeHandle($db);
		
		$this->entityManager->merge($db);
		$this->entityManager->flush();
		$this->invalidateEntityCache($db);
	    }
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex->getMessage());
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

}
