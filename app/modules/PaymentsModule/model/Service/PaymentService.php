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

namespace App\PaymentsModule\Model\Service;

use App\Model\Entities\Payment,
    \Nette\DateTime,
    App\Model\Entities\SeasonApplication,
    \Kdyby\Doctrine\EntityManager,
    \App\Services\Exceptions\NullPointerException,
    \App\Services\Exceptions,
    \App\Services\Exceptions\DataErrorException,
    \Kdyby\Doctrine\DuplicateEntryException,
    \Nette\InvalidArgumentException,
    \Kdyby\Doctrine\DBALException,
    \App\Model\Service\BaseService,
    \App\SeasonsModule\Model\Service\ISeasonService,
    \App\SeasonsModule\Model\Service\ISeasonTaxService,
    \App\PaymentsModule\Model\Service\IPaymentService,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\Model\Service\IUserService,
    \Grido\DataSources\Doctrine,
    \App\Model\Entities\SportGroup;

/**
 * Description of Payment service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class PaymentService extends BaseService implements IPaymentService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $paymentDao;

    /**
     * @var \App\Model\Service\IUserService,
     */
    private $usersService;

    /**
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    private $seasonService;

    public function getUsersService() {
	return $this->usersService;
    }

    public function getSeasonService() {
	return $this->seasonService;
    }

    public function setUsersService(IUserService $usersService) {
	$this->usersService = $usersService;
    }

    public function setSeasonService(ISeasonService $seasonService) {
	$this->seasonService = $seasonService;
    }

    function __construct(EntityManager $em) {
	parent::__construct($em, Payment::getClassName());
	$this->paymentDao = $em->getDao(Payment::getClassName());
    }

    public function createPayment(Payment $p) {
	if ($p === NULL)
	    throw new NullPointerException("Argument Payment was null.", 0);
	try {
	    $now = new DateTime();
	    $this->entityManager->beginTransaction();
	    $this->paymentOwnerTypeHandle($p);
	    $this->paymentSeasonTypeHandle($p);
	    $this->paymentEditorTypeHandle($p);
	    $p->setOrderedDate($now);
	    $this->paymentDao->save($p);
	    $this->invalidateEntityCache($p);
	    $this->entityManager->commit();
	} catch (DuplicateEntryException $ex) {
	    throw new Exceptions\DuplicateEntryException($ex);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    private function paymentOwnerTypeHandle(Payment $p) {
	if ($p === NULL)
	    throw new NullPointerException("Argument Payment was null.", 0);
	try {
	    $oId = $this->getMixId($p->getOwner());
	    if ($oId !== null) {
		$owner = $this->getUsersService()->getUser($oId, false);
		$p->setOwner($owner);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $p;
    }

    private function paymentEditorTypeHandle(Payment $p) {
	if ($p === NULL)
	    throw new NullPointerException("Argument Payment was null.", 0);
	try {
	    $rId = $this->getMixId($p->getEditor());
	    if ($this->getUsersService() !== null && $rId !== null) {
		$editor = $this->getUsersService()->getUser($rId, false);
		$p->setEditor($editor);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $p;
    }

    private function paymentSeasonTypeHandle(Payment $p) {
	if ($p === NULL)
	    throw new NullPointerException("Argument Payment was null.", 0);
	try {
	    $sId = $this->getMixId($p->getSeason());
	    if ($this->getSeasonService() !== null && $sId !== null) {
		$season = $this->getSeasonService()->getSeason($sId, false);
		$p->setSeason($season);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $p;
    }

    public function getPayment($id, $useCache = true) {
	if ($id === NULL)
	    throw new NullPointerException("Argument id was null.", 0);
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric", 1);
	try {
	    if (!$useCache) {
		return $this->paymentDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->paymentDao->find($id);
		//$opt = [Cache::TAGS => [$this->getEntityClassName(), $id]];
		$opt = [Cache::TAGS => [self::ENTITY_COLLECTION, self::SELECT_COLLECTION, $id]];
		$cache->save($id, $data, $opt);
	    }
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	return $data;
    }

    public function deletePayment($id) {
	if ($id === NULL)
	    throw new NullPointerException("Argument Payment was null.", 0);
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument id has to be type of numeric", 1);
	try {
	    $payment = $this->paymentDao->find($id, false);
	    if ($payment !== null) {
		$this->paymentDao->delete($payment);
		$this->invalidateEntityCache($payment);
	    }
	} catch (DBALException $ex) {
	    throw new DataErrorException($ex->getMessage(), 1000, $ex);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function markAsDone(Payment $p) {
	if ($p === NULL)
	    throw new NullPointerException("Argument Payment was null.", 0);
	throw new \Nette\NotImplementedException("How should this method work? Update payment is not enough?");
//TODO
    }

    public function updatePayment(Payment $p) {
	if ($p === NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Payment was null.", 0);
	if ($p === NULL)
	    throw new NullPointerException("Argument Payment was null.", 0);
	try {
	    $this->entityManager->beginTransaction();
	    $paymentDb = $this->paymentDao->find($p->getId(), false);
	    if ($paymentDb !== null) {
		$paymentDb->fromArray($p->toArray());
		$this->paymentOwnerTypeHandle($paymentDb);
		$this->paymentSeasonTypeHandle($paymentDb);
		$this->paymentEditorTypeHandle($paymentDb);
		$this->paymentDao->save($paymentDb);
		$this->invalidateEntityCache($paymentDb);
	    }
	    $this->entityManager->commit();
	} catch (DuplicateEntryException $ex) {
	    throw new Exceptions\DuplicateEntryException($ex);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function getPaymentsDatasource() {
	$model = new Doctrine(
		$this->paymentDao->createQueryBuilder('pa'));
	return $model;
    }

// plan functionality of users payments
}
