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

use \App\Model\Entities\Payment,
    \App\Model\Misc\Enum\PaymentStatus,
    \Kdyby\GeneratedProxy\__CG__\App\Model\Entities,
    \Nette\DateTime,
    \Nette\Caching\Cache,
    \Kdyby\Monolog\Logger,
    \Kdyby\Doctrine\EntityManager,
    \App\Model\Misc\Exceptions,
    \App\Services\Exceptions\DataErrorException,
    \Kdyby\Doctrine\DuplicateEntryException,
    \Kdyby\Doctrine\DBALException,
    \App\Model\Service\BaseService,
    \App\SeasonsModule\Model\Service\ISeasonService,
    \App\PaymentsModule\Model\Service\IPaymentService,
    \App\UsersModule\Model\Service\IUserService,
    \Grido\DataSources\Doctrine;

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
     * @var \App\UsersModule\Model\Service\IUserService,
     */
    private $usersService;

    /**
     * @var \App\SeasonsModule\Model\Service\ISeasonService
     */
    private $seasonService;
    
    /**
     * Default due date DateTime modifier
     * @var string
     */
    private $dueDate;
    
    /** @var Event dispatched every time after create of Payment */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of Payment */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of Payment */
    public $onDelete = [];
    
    public function getDefaultDueDate() {
	return new DateTime($this->dueDate);
    }

    public function setDefaultDueDate($dueDate) {
	if (!\Nette\Utils\Strings::contains($dueDate, "+")) {
	   $dueDate = "+ ".$dueDate;
	}
	$this->dueDate = $dueDate;
    }

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

    function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, Payment::getClassName(), $logger);
	$this->paymentDao = $em->getDao(Payment::getClassName());
    }

    public function createPayment(Payment $p) {
	if ($p === NULL)
	    throw new Exceptions\NullPointerException("Argument Payment was null.");
	try {
	    $now = new DateTime();
	    $this->entityManager->beginTransaction();
	    $this->paymentOwnerTypeHandle($p);
	    $this->paymentSeasonTypeHandle($p);
	    $this->paymentEditorTypeHandle($p);
	    $p->setOrderedDate($now);
	    if ($p->getVs() === null) $p->setVs($p->getOwner()->getBirthNumber());
	    $this->paymentDao->save($p);
	    $this->invalidateEntityCache($p);
	    $this->entityManager->commit();
	    
	    $this->onCreate($p);
	} catch (DuplicateEntryException $ex) {
	    $this->entityManager->rollback();
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    private function paymentOwnerTypeHandle(Payment $p) {
	if ($p === NULL)
	    throw new Exceptions\NullPointerException("Argument Payment was null.", 0);
	try {
	    $oId = $this->getMixId($p->getOwner());
	    if ($oId !== null) {
		$owner = $this->getUsersService()->getUser($oId, false);
		$p->setOwner($owner);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $p;
    }

    private function paymentEditorTypeHandle(Payment $p) {
	if ($p === NULL)
	    throw new Exceptions\NullPointerException("Argument Payment was null.", 0);
	try {
	    $rId = $this->getMixId($p->getEditor());
	    if ($this->getUsersService() !== null && $rId !== null) {
		$editor = $this->getUsersService()->getUser($rId, false);
		$p->setEditor($editor);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $p;
    }

    private function paymentSeasonTypeHandle(Payment $p) {
	if ($p === NULL)
	    throw new Exceptions\NullPointerException("Argument Payment was null.", 0);
	try {
	    $sId = $this->getMixId($p->getSeason());
	    if ($this->getSeasonService() !== null && $sId !== null) {
		$season = $this->getSeasonService()->getSeason($sId, false);
		$p->setSeason($season);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $p;
    }

    public function getPayment($id, $useCache = true) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric", 1);
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
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $data;
    }

    public function deletePayment($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    $payment = $this->paymentDao->find($id, false);
	    if ($payment !== null && $payment->getStatus() !== PaymentStatus::SENT) {
		$this->paymentDao->delete($payment);
		$this->invalidateEntityCache($payment);
		$this->onDelete($payment);
	    }
	} catch (DBALException $ex) {
	    $this->logWarning($ex);
	    throw new DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updatePayment(Payment $p) {
	if ($p === NULL)
	    throw new Exceptions\NullPointerException("Argument Payment was null.");
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
		$this->onUpdate($paymentDb);
	    }
	    $this->entityManager->commit();
	} catch (DuplicateEntryException $ex) {
	    $this->entityManager->rollback();
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getPaymentsDatasource(Entities\User $u = null) {
	
	$model = $this->paymentDao->createQueryBuilder('pa');
	if ($u !== null) {
	    $model->where("pa.owner = :id")->setParameter("id", $u->getId());
	}
	return new Doctrine($model);
    }

    public function markAsDoneSent($id, Entities\User $user) {
	if (!is_numeric($id)) throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	$this->markAs($id, $user, PaymentStatus::SENT);
    }
    
    public function markAsDoneAcc($id, Entities\User $user) {
	if (!is_numeric($id)) throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	$this->markAs($id, $user, PaymentStatus::YES_ACCOUNT);
    }
    
    public function markAsDoneCash($id, Entities\User $user) {
	if (!is_numeric($id)) throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	$this->markAs($id, $user, PaymentStatus::YES_CASH);
    }
    
    public function generateVs(Payment $p) {
	if ($p == null) return rand (1000, 99999);
	if (empty($p->getVs())) return $p->getOwner()->getBirthNumber();
	return $p->getVs();
    }
   
    private function markAs($id, Entities\User $user, $as) {
	try {
	    $db = $this->paymentDao->find($id);
	    if ($db !== null) {
		$db->setStatus($as);
		$db->setEditor($user);
		$this->paymentEditorTypeHandle($db);
		$this->entityManager->merge($db);
		$this->entityManager->flush();
		$this->invalidateEntityCache($db);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException(
		    $ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

}
