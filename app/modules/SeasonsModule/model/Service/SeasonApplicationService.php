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

use \App\Model\Entities\SeasonApplication,
	\Nette\DateTime,
	Nette\Caching\Cache,
    \Kdyby\Doctrine\EntityManager,
    \App\Services\Exceptions\NullPointerException,
    \App\Services\Exceptions,
    \App\Services\Exceptions\DataErrorException,
    \Kdyby\Doctrine\DuplicateEntryException,
    \Nette\InvalidArgumentException,
    \App\Model\Service\BaseService,
    \App\SeasonsModule\Model\Service\ISeasonService,
    \App\SeasonsModule\Model\Service\ISeasonTaxService,
    App\PaymentsModule\Model\Service\IPaymentService,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\Model\Service\IUserService,
    \Grido\DataSources\Doctrine;

/**
 * Service for managing season related entities
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class SeasonApplicationService extends BaseService implements ISeasonApplicationService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $seasonApplicationDao;

    /**
     * @var \App\SeasonModule\Model\Service\ISeasonService
     */
    private $seasonService;

    /**
     * @var \App\SeasonModule\Model\Service\ISeasonTaxService
     */
    private $seasonTaxService;

    /**
     * @var \App\PaymentModule\Model\Service\IPaymentService
     */
    private $paymentService;

    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;

    /**
     * @var \App\Model\Service\IUserService
     */
    private $userService;
    
    public $onCreate = [];

    public function getUserService() {
	return $this->userService;
    }

    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }

    public function getSeasonService() {
	return $this->seasonService;
    }

    public function getSeasonTaxService() {
	return $this->seasonTaxService;
    }

    public function getPaymentService() {
	return $this->paymentService;
    }

    public function setSeasonService(ISeasonService $seasonService) {
	$this->seasonService = $seasonService;
    }

    public function setSeasonTaxService(ISeasonTaxService $seasonTaxService) {
	$this->seasonTaxService = $seasonTaxService;
    }

    public function setPaymentService(IPaymentService $paymentService) {
	$this->paymentService = $paymentService;
    }

    public function getSportGroupService() {
	return $this->sportGroupService;
    }

    public function setSportGroupService(ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }

    public function __construct(EntityManager $em) {
	parent::__construct($em, SeasonApplication::getClassName());
	$this->seasonApplicationDao = $em->getDao(SeasonApplication::getClassName());
    }

    public function createSeasonApplication(SeasonApplication $app) {
	if ($app == null)
	    throw new NullPointerException("Argument SeasonApplication cannot be null", 0);
	try {
	    $now = new DateTime();
	    $this->applicationSeasonTypeHandle($app);
	    //$this->applicationPaymentTypeHandle($app); // TODO create new payment
	    $this->applicationGroupTypeHandle($app);
	    $this->applicationOwnerTypeHandle($app);
	    $this->applicationEditorTypeHandle($app);
	    $app->setUpdated($now);
	    $app->setEnrolledTime($now);
	    $this->seasonApplicationDao->save($app);
	    $this->invalidateEntityCache($app);
	} catch (DuplicateEntryException $ex) {
	    throw new Exceptions\DuplicateEntryException($ex);
	} catch (Exception $ex) {
	    throw new DataErrorException();
	}
	$this->onCreate(clone $app);
    }
    
    function getSeasonApplication($id, $useCache = true) {
	if ($id === null)
	    throw new NullPointerException("Argument id cannot be null", 0);
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    if (!$useCache) {
		return $this->seasonApplicationDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->seasonApplicationDao->find($id);
		$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $data;
    }
    

    private function applicationSeasonTypeHandle(SeasonApplication $app) {
	if ($app === null)
	    throw new NullPointerException("Argument SeasonApplication cannot be null", 0);
	try {
	    $id = $this->getMixId($app->getSeason());
	    $season = $this->getSeasonService()->getSeason($id, false);
	    $app->setSeason($season);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    private function applicationPaymentTypeHandle(SeasonApplication $app) {
	if ($app === null)
	    throw new NullPointerException("Argument SeasonApplication cannot be null", 0);
	try {
	    $payment = null;
	    if ($this->getPaymentService() !== null) {
		$id = $this->getMixId($app->getPayment());
		if ($id !== null) {
		    $payment = $this->getPaymentService()->getPayment($id, false);
		}
	    }
	    $app->setPayment($payment);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    private function applicationGroupTypeHandle(SeasonApplication $app) {
	if ($app === null)
	    throw new NullPointerException("Argument SeasonApplication cannot be null", 0);
	try {
	    $group = null;
	    if ($this->getSportGroupService() !== null) {
		$id = $this->getMixId($app->getSportGroup());
		$group = $this->getSportGroupService()->getSportGroup($id, false);
	    }
	    $app->setSportGroup($group);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    private function applicationOwnerTypeHandle(SeasonApplication $app) {
	if ($app === null)
	    throw new NullPointerException("Argument SeasonApplication cannot be null", 0);
	try {
	    $owner = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($app->getOwner());
		if ($id !== null) {
		    $owner = $this->getUserService()->getUser($id, false);
		}
	    }
	    $app->setOwner($owner);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    private function applicationEditorTypeHandle(SeasonApplication $app) {
	if ($app === null)
	    throw new NullPointerException("Argument SeasonApplication cannot be null", 0);
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($app->getEditor());
		if ($id !== null) {
		    $editor = $this->getUserService()->getUser($id, false);
		}
	    }
	    $app->setEditor($editor);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function deleteSeasonApplication($id) {
	if ($id === null)
	    throw new NullPointerException("Argument id cannot be null", 0);
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    $appDb = $this->seasonApplicationDao->find($id);
	    if ($appDb !== null) {
		$this->seasonApplicationDao->delete($appDb);
		$this->invalidateEntityCache($appDb);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function updateSeasonApplication(SeasonApplication $app) {
	if ($app === null)
	    throw new NullPointerException("Argument SeasonApplication cannot be null", 0);
	try {
	    $this->entityManager->beginTransaction();
	    $appDb = $this->seasonApplicationDao->find($app->getId(), false);
	    if ($appDb !== null) {
		$appDb->fromArray($app->toArray());
		$this->applicationSeasonTypeHandle($appDb);
		$this->applicationPaymentTypeHandle($appDb);
		$this->applicationGroupTypeHandle($appDb);
		$this->applicationOwnerTypeHandle($appDb);
		$this->applicationEditorTypeHandle($appDb);
		$appDb->setUpdated(new DateTime());
		$this->entityManager->merge($appDb);
		$this->entityManager->flush();
	    }
	    $this->entityManager->commit();
	    $this->invalidateEntityCache($appDb);
	} catch (DuplicateEntryException $ex) {
	    throw new Exceptions\DuplicateEntryException($ex);
	} catch (Exception $ex) {
	    throw new DataErrorException();
	}
    }

    public function getSeasonApplicationsDataSource() {
	$model = new Doctrine(
		$this->seasonApplicationDao->createQueryBuilder('sa'));
	return $model;
	// TODO pripadne pridelat where season id id ....
    }

}
