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

namespace App\UsersModule\Model\Service;

use \App\UsersModule\Model\Service\IUserService,
    \Kdyby\Doctrine\EntityManager,
    \Kdyby\Doctrine\DBALException,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\DuplicateEntryException,
    \App\Model\Misc\Exceptions,
    \Nette\DateTime,
    \App\Misc\Passwords,
    \Nette\Utils\Strings,
    \Nette\Caching\Cache,
    \Grido\DataSources\Doctrine,
    \App\Model\Misc\Enum\WebProfileStatus,
    \App\Model\Entities\User,
    \App\Model\Entities\Address,
    \App\Model\Entities\Contact,
    \App\Model\Entities\WebProfile,
    \App\Model\Service\INotificationService;

/**
 * Service for dealing with User related entities
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class UserService extends BaseService implements IUserService {

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $userDao;

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $addressDao;

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $contactDao;

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $webProfileDao;
    
    /**
     * @var \App\Model\Service\INotificationService
     */
    private $notifService;
    
    /**
     * @var string
     */
    private $salt;
    
    
    public function setSalt($salt) {
	if (empty($salt))
	    throw new Exceptions\InvalidArgumentException("Argument salt has to be non empty string", 1);
	$this->salt = $salt;
    }
    
    public function setNotifService(INotificationService $ns) {
	$this->notifService = $ns;
    }

    public function __construct(EntityManager $em) {
	parent::__construct($em, User::getClassName());
	$this->userDao = $em->getDao(User::getClassName());
	$this->addressDao = $em->getDao(Address::getClassName());
	$this->contactDao = $em->getDao(Contact::getClassName());
	$this->webProfileDao = $em->getDao(WebProfile::getClassName());
    }

    public function createUser(User $user) {
	if ($user == null)
	    throw new Exceptions\NullPointerException("Argument User cannot be null", 0);

	$this->entityManager->beginTransaction();
	
	$newPassword = Strings::random();
	$now = new DateTime();
	
	$hashedPassword = Passwords::hash($newPassword, ['salt' => $this->salt]);
	$user->setPassword($hashedPassword);
	$user->setCreated($now);
	$user->setWebProfile(new WebProfile());
	$user->contact->setUpdated($now);
	$user->getWebProfile()->setUpdated($now);
	$user->setProfileStatus(WebProfileStatus::BAD);

	try {
	    $this->contactDao->save($user->getContact());
	} catch (DuplicateEntryException $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}

	try {
	    $this->userDao->save($user);
	    $this->entityManager->commit();
	} catch (DuplicateEntryException $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	$this->logInfo("User %user was successfully created", ["user"=>$user]);
	$this->notifService->newAccountNotification($user);
    }

    public function updateUser(User $formUser) {
	if ($formUser == null)
	    throw new Exceptions\NullPointerException("Argument User cannot be null", 0);
	
	$this->entityManager->beginTransaction();

	$uDb = $this->getUser($formUser->id);
	if ($uDb !== null) {

	    try {
		$uDbContact = $uDb->getContact();

		$formUser->getContact()->setUpdated(new DateTime());
		$formUser->getContact()->setId($uDbContact->getId());

		$this->entityManager->merge($formUser->getContact());
		$this->entityManager->flush();
	    } catch (DuplicateEntryException $e) {
		$this->entityManager->rollback();
		throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	    }

	    try {
		$formUser->setWebProfile($uDb->getWebProfile());
		$formUser->setCreated($uDb->getCreated());
		$formUser->setLastLogin($uDb->getLastLogin());
		$uArray = $formUser->toArray();
		$uDb->fromArray($uArray);
		$this->entityManager->merge($uDb);
		$this->entityManager->flush();
	    } catch (DuplicateEntryException $e) {
		$this->entityManager->rollback();
		throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	    } catch (\Exception $e) {
		$this->entityManager->rollback();
		throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	    }
	    $this->entityManager->commit();
	}
	
    }

    public function deleteUser($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument Id must be type of numeric, '$id' given", 1);

	$this->entityManager->beginTransaction();
	try {
	    $db = $this->getUser($id);
	    if ($db !== null) {
		$this->userDao->delete($db);
	    } else {
		throw new EntityNotFoundException("User with id '$id' does not exist", 2);
	    }
	    $this->entityManager->commit();
	} catch (DBALException $ex) {
	    throw new Exceptions\DependencyException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getUser($id) {
	if ($id == NULL)
	    throw new Exceptions\NullPointerException("Argument Id cannot be null", 0);
	if (!is_numeric($id))
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, '$id' given", 1);
	try {
	    return $this->userDao->find($id);
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getUsers() {
	try {
	    return $this->userDao->findAll();
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    /**
     * Returns datasource for grido datagrid
     * @return \Grido\DataSources\Doctrine
     */
    public function getUsersDatasource() {
	$model = new Doctrine(
		$this->userDao->createQueryBuilder('u'));
	return $model;
    }

    public function getUserEmail($email) {
	$qb = $this->entityManager->createQueryBuilder();
	$qb->select('u')
		->from('App\Model\Entities\User', 'u')
		->innerJoin('u.contact', 'c')
		->where('c.email = :email')
		->setParameter("email", $email);
	try {
	    return $qb->getQuery()->getSingleResult();
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function getSelectUsers($id = null) {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	try {
	    if ($data === null) {
		$data = $this->userDao->findPairs(["active"=>1], 'surname'); // TODO CONCAT
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    if ($id != null) {
		unset($data[$id]);
	    }
	    return $data;
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function regeneratePassword($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	
	try {
	    $this->entityManager->beginTransaction();
	    $user = $this->getUser($id, false);
	    $newPw = Strings::random();
	    $options = ["salt" => $this->salt];
	    $hash = Passwords::hash($newPw, $options);
	    $user->setPassword($hash);
	    $user->setPasswordChangeRequired(true);
	    $this->entityManager->merge($user);
	    $this->entityManager->flush();
	    $this->invalidateEntityCache($user);
	    $this->entityManager->commit();
	} catch (\Exception $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	
	$this->notifService->sendPasswordNotification($user);
	return $hash;
    }
    
    public function toggleUser($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	    try {
		$this->entityManager->beginTransaction();
		$user = $this->getUser($id, false);
		$user->setActive($result = !$user->getActive());
		$this->entityManager->merge($user);
		$this->entityManager->flush();
		$this->invalidateEntityCache($user);
		$this->entityManager->commit();
	    } catch (\Exception $e) {
		$this->entityManager->rollback();
		throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	    }    
	$this->notifService->sendToggleNotification($user);
    }
}
