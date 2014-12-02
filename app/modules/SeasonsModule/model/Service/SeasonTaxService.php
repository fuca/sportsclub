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

use \Nette\DateTime,
    \Nette\Caching\Cache,
    \App\Model\Entities\SeasonTax,
    \App\Model\Entities\SportGroup,
    \App\Model\Entities\Season,
    \Kdyby\Monolog\Logger,
    \Doctrine\ORM\NoResultException,
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\Model\Misc\Exceptions,
    \Grido\DataSources\Doctrine,
    \App\Model\Service\BaseService,
    \App\UsersModule\Model\Service\IUserService,
    \App\SeasonsModule\Model\Service\ISeasonService,
    \App\SystemModule\Model\Service\ISportGroupService,
    \Kdyby\Doctrine\EntityManager,
    \App\SeasonsModule\Model\Service\ISeasonTaxService;

/**
 * Service for managing season related entities
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class SeasonTaxService extends BaseService implements ISeasonTaxService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $seasonTaxDao;

    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;

    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;

    /**
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    private $seasonService;
    
    /** @var Event dispatched every time after create of SeasonTax */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of SeasonTax */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of SeasonTax */
    public $onDelete = [];

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, SeasonTax::getClassName(), $logger);
	$this->seasonTaxDao = $em->getDao(SeasonTax::getClassName());
    }
    
    public function getSeasonService() {
	return $this->seasonService;
    }

    public function setSeasonService(ISeasonService $seasonService) {
	$this->seasonService = $seasonService;
    }

    public function getSportGroupService() {
	return $this->sportGroupService;
    }

    public function getUserService() {
	return $this->userService;
    }

    public function setSportGroupService(ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }

    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }

    public function createSeasonTax(SeasonTax $t) {
	if ($t === null)
	    throw new Exceptions\NullPointerException("Argument SeasonTax cannot be null", 0);
	try {
	    $this->taxSeasonTypeHandle($t);
	    $this->taxSportGroupTypeHandle($t);
	    $this->taxEditorTypeHandle($t);
	    $t->setChanged(new DateTime());

	    $this->seasonTaxDao->save($t);
	    $this->invalidateEntityCache($t);
	    $this->onCreate($t);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException(
		    $ex->getMessage(), Exceptions\DuplicateEntryException::SEASON_TAX, $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    private function taxSeasonTypeHandle(SeasonTax $t) {
	if ($t == null)
	    throw new Exceptions\NullPointerException("Argument SeasonTax cannot be null", 0);
	try {
	    $season = $this->getMixId($t->getSeason());
	    $sportGroup = $this->getSeasonService()->getSeason($season, false);
	    $t->setSeason($sportGroup);
	    return $t;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    private function taxSportGroupTypeHandle(SeasonTax $t) {
	if ($t == null)
	    throw new Exceptions\NullPointerException("Argument SeasonTax cannot be null", 0);
	try {
	    $group = $this->getMixId($t->getSportGroup());
	    $sportGroup = $this->getSportGroupService()
		    ->getSportGroup((integer) $group, false);
	    $t->setSportGroup($sportGroup);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $t;
    }

    private function taxEditorTypeHandle(SeasonTax $t) {
	if ($t == null)
	    throw new Exceptions\NullPointerException("Argument SeasonTax cannot be null", 0);
	try {
	    $u = $this->getMixId($t->getEditor());
	    if ($u !== null) {
		$editor = $this->getUserService()->getUser($u, false);
		$t->setEditor($editor);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $t;
    }

    public function deleteSeasonTax($id) {
	if ($id == null)
	    throw new Exceptions\NullPointerException("Argumen id cannot be null", 0);
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric", 1);
	try {
	    $db = $this->seasonTaxDao->find($id);
	    if ($db !== null) {
		$this->seasonTaxDao->delete($db);
		$this->invalidateEntityCache($db);
		$this->onDelete($db);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateSeasonTax(SeasonTax $t) {
	if ($t == null)
	    throw new Exceptions\NullPointerException("Argument SeasonTax cannot be null", 0);
	try {
	    $this->entityManager->beginTransaction();
	    $tDb = $this->seasonTaxDao->find($t->getId());
	    if ($tDb !== null) {
		$tDb->fromArray($t->toArray());
		$this->taxSeasonTypeHandle($tDb);
		$this->taxSportGroupTypeHandle($tDb);
		$this->taxEditorTypeHandle($tDb);
		$tDb->setChanged(new DateTime());
		
		$this->entityManager->merge($tDb);
		$this->entityManager->flush();
		
	    }
	    $this->entityManager->commit();
	    $this->invalidateEntityCache($t);
	    $this->onUpdate($t);
	} catch (DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException(
		    $ex->getMessage(), Exceptions\DuplicateEntryException::SEASON_TAX, $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getSeasonTaxesDataSource() {
	$model = new Doctrine(
		$this->seasonTaxDao->createQueryBuilder('tax'));
	return $model;
    }

    public function getSeasonTax($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric", 1);
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);

	    if ($data === null) {
		$data = $this->seasonTaxDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $data;
    }
    
    public function getSeasonTaxSG(Season $s, SportGroup $sg) {
	try {
	    $res = $this->seasonTaxDao->createQueryBuilder("st")
			->where("st.season = :season")
			->setParameter("season", $s)
			->andWhere("st.sportGroup = :group")
			->setParameter("group", $sg)
			->getQuery()->getSingleResult();
	    return $res;
	} catch (NoResultException $ex) {
	    $this->logWarning($ex->getMessage());
	    throw new Exceptions\NoResultException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
}
