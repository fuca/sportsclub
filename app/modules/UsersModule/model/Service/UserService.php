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
    \Doctrine\ORM\NoResultException,
    \App\Model\Misc\Exceptions,
    \Nette\DateTime,
    \Nette\Security\Passwords,
    \Nette\Utils\Strings,
    \Nette\Caching\Cache,
    \Grido\DataSources\Doctrine,
    \App\Model\Misc\Enum\WebProfileStatus,
    \App\Model\Entities\User,
    \Kdyby\GeneratedProxy\__CG__\App\Model\Entities,
    \App\Model\Entities\Address,
    \App\Model\Entities\Contact,
    \App\Model\Entities\WebProfile,
    \Doctrine\Common\Collections\Criteria,
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
    
    public function getSalt() {
	return $this->salt;
    }

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
    
    public function generateNewPassword($word = null) {
	$word = $word? $word: Strings::random();
	$o = ['salt'=>$this->salt, 'cost'=>4];
	$hashedPassword = Passwords::hash($word, $o);
	return $hashedPassword;
    }

    public function createUser(User $user) {
	if ($user == null)
	    throw new Exceptions\NullPointerException("Argument User cannot be null", 0);

	$this->entityManager->beginTransaction();
	$now = new DateTime();
	
	$user->setPassword($this->generateNewPassword());
	$user->setCreated($now);
	$user->setUpdated($now);
	$user->setWebProfile(new WebProfile());
	$user->contact->setUpdated($now);
	$user->getWebProfile()->setUpdated($now);
	$user->setProfileStatus(WebProfileStatus::BAD);

	try {
	    $this->contactDao->save($user->getContact());
	} catch (DuplicateEntryException $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException(
			$e->getMessage(), 
			Exceptions\DuplicateEntryException::EMAIL_EXISTS, 
			$e->getPrevious());
	}

	try {
	    $this->userDao->save($user);
	    $this->entityManager->commit();
	} catch (DuplicateEntryException $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException(
			$e->getMessage(), 
			Exceptions\DuplicateEntryException::BIRTH_NUM_EXISTS, 
			$e->getPrevious());
	} catch (\Exception $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	$this->logInfo("User %user was successfully created", ["user" => $user]);
	$this->notifService->newAccountNotification($user);
    }

    public function updateUser(User $formUser) {
	if ($formUser == null)
	    throw new Exceptions\NullPointerException("Argument User cannot be null", 0);

	$this->entityManager->beginTransaction();

	$uDb = $this->getUser($formUser->id);
	if ($uDb !== null) {

	    $this->handleUpdateContact($uDb, $formUser);
	    $this->handleUpdateUser($uDb, $formUser);

	    $this->entityManager->commit();
	}
    }

    /**
     * Calls rollback
     * @param \App\Model\Entities\User $uDb
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    private function handleUpdateContact(User $uDb, User $formUser) {
	try {
	    $now = new DateTime();
	    $formUser->getContact()->setUpdated($now);
	    $uDb->getContact()->fromArray($formUser->getContact()->toArray());
	    $this->entityManager->merge($uDb->getContact());
	    $this->entityManager->flush();
	} catch (DuplicateEntryException $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    /**
     * Calls rollback
     * @param \App\Model\Entities\User $uDb
     * @param \App\Model\Entities\User $formUser
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    private function handleUpdateUser(User $uDb, User $formUser) {
	try {
	    $now = new DateTime();
	    if($formUser->getWebProfile() === null) {
		$formUser->setWebProfile($uDb->getWebProfile());	
	    } else {
		$uDb->getWebProfile()->fromArray($formUser->getWebProfile()->toArray());
	    }
	    $formUser->getWebProfile()->setUpdated($now);
	    $formUser->setCreated($uDb->getCreated());
	    $formUser->setUpdated($now);
	    $formUser->setLastLogin($uDb->getLastLogin());
	    $formUser->setContact($uDb->getContact());

	    $uDb->fromArray($formUser->toArray());
	    $this->entityManager->merge($uDb);
	    $this->entityManager->flush();
	} catch (DuplicateEntryException $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
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

    public function getWebProfilesToPermitDatasource() {
	$c = new Criteria();
	$c->where(Criteria::expr()->eq("status", WebProfileStatus::UPDATED));
	$model = new Doctrine(
		$this->webProfileDao->createQueryBuilder('u')
			->addCriteria($c));
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
	} catch (NoResultException $e) {
	    throw new Exceptions\NoResultException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    /**
     * Null active means all users, bool active means users with the same active value
     * @param inteter $id
     * @param bool|null $active
     * @return array of pairs
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    public function getSelectUsers($id = null, $active = true) {
	if (!is_bool($active) && !is_null($active)) 
	    throw new Exceptions\InvalidArgumentException("Argument active has to be type of boolean or null, '$active' given");
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	try {
	    if ($data === null) {
		$data = [];
		$all = $this->userDao->findAll(["active" => $active]);
		foreach ($all as $u) {
		    if (!is_null($active) && $active !== $u->getActive()) continue;
		    $data = $data+[$u->getId() => $u->getName()." ".$u->getSurname()." (".$u->getId().")"];
		}
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

    public function permitWebProfile($id, Entities\User $u) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    $wpDb = $this->webProfileDao->find($id);
	    if ($wpDb !== null) {
		$wpDb->setStatus(WebProfileStatus::OK);
		$wpDb->setUpdated(new \Nette\Utils\DateTime());
		$wpDb->setEditor($u);
		$this->editorTypeHandle($wpDb);
		$this->entityManager->merge($wpDb);
		$this->entityManager->flush();
	    }
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function denyWebProfile($id, Entities\User $u) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given", 1);
	try {
	    $wpDb = $this->webProfileDao->find($id);
	    if ($wpDb !== null) {
		$wpDb->setStatus(WebProfileStatus::BAD);
		$wpDb->setUpdated(new \Nette\Utils\DateTime());
		$wpDb->setEditor($u);
		$this->editorTypeHandle($wpDb);
		$this->entityManager->merge($wpDb);
		$this->entityManager->flush();
	    }
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    private function editorTypeHandle(WebProfile $e) {
	if ($e === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null", 0);
	try {
	    $editor = null;
	    $id = $this->getMixId($e->getEditor());
	    if ($id !== null) $editor = $this->userDao->find($id);
	    $e->setEditor($editor);
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex);
	}
    }

}
