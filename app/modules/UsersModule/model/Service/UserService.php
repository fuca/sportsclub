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
    \App\Model\Entities\Address,
    \App\Model\Entities\Contact,
    \App\Model\Entities\WebProfile,
    \Doctrine\Common\Collections\Criteria,
    \Tomaj\Image\ImageService;

/**
 * Service for dealing with User related entities
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
class UserService extends BaseService implements IUserService {
    
    const RANDOM_PASS_LENGTH = 8;

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
     * @var string
     */
    private $salt;
    
    /**
     * @var \Tomaj\Image\ImageService
     */
    private $imageService;
    
    /** @var Event dispatched every time after create of User */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of User, WebProfile */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of User */
    public $onDelete = [];
    
    /** @var Event dispatched every time after activation of User account */
    public $onActivate = [];
    
    /** @var Event dispatched every time after deactivation of User account */
    public $onDeactivate = [];
    
    /** @var Event dispatched every time after password regeneration */
    public $onPasswordRegenerate = [];

    public function __construct(EntityManager $em) {
	parent::__construct($em, User::getClassName());
	$this->userDao = $em->getDao(User::getClassName());
	$this->addressDao = $em->getDao(Address::getClassName());
	$this->contactDao = $em->getDao(Contact::getClassName());
	$this->webProfileDao = $em->getDao(WebProfile::getClassName());
    }
    
    public function setImageService(ImageService $imageService) {
	$this->imageService = $imageService;
    }
    
    public function getSalt() {
	return $this->salt;
    }

    public function setSalt($salt) {
	if (empty($salt))
	    throw new Exceptions\InvalidArgumentException("Argument salt has to be non empty string", 1);
	$this->salt = $salt;
    }
    
    public function generateNewPassword($word = null) {
	$word = $word? $word: Strings::random(self::RANDOM_PASS_LENGTH);
	$o = ['salt'=>$this->salt, 'cost'=>4];
	$hashedPassword = Passwords::hash($word, $o);
	return $hashedPassword;
    }

    public function createUser(User $user) {

	$now = new DateTime();
	$rawPass = null;
	
	if (empty($user->getPassword())) {
	    $rawPass = Strings::random(self::RANDOM_PASS_LENGTH);    
	} else {
	    $rawPass = $user->getPassword();
	}
	$this->entityManager->beginTransaction();
	
	$user->setPassword($this->generateNewPassword($rawPass));
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
	    $this->logError($e->getMessage());
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	$this->logInfo("User %user was successfully created", ["user" => $user]);
	$user->insertRawPassword($rawPass);
	$this->onCreate(clone $user);
    }

    public function updateUser(User $formUser) {
	if ($formUser == null)
	    throw new Exceptions\NullPointerException("Argument User cannot be null", 0);

	$this->entityManager->clear(User::getClassName());
	$this->entityManager->beginTransaction();
	$uDb = null;
	$id = $formUser->getId();
	try {
	    $uDb = $this->getUser($id, false);
	    
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	
	if ($uDb !== null) {
	    $this->handleUpdateContact($uDb, $formUser);
	    $this->handleUpdateUser($uDb, $formUser);
	    $this->invalidateEntityCache($uDb);
	    $this->entityManager->commit();
	    $this->onUpdate(clone $uDb);
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
	    //$this->entityManager->flush();
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
	$identifier = null;
	try {
	    $now = new DateTime();
	    if ($formUser->getWebProfile() === null) {
		$formUser->setWebProfile($uDb->getWebProfile());	
	    } else {
		if ($formUser->getWebProfile()->getPicture() == "") {
		    $formUser->getWebProfile()->setPicture($uDb->getWebProfile()->getPicture());
		} else {
		    $origId = $uDb->getWebProfile()->getPicture();
		    $this->imageService->removeResource($origId);
		    
		    $identifier = $this->imageService
			    ->storeNetteFile($formUser->getWebProfile()->getPicture());
		    $formUser->getWebProfile()->setPicture($identifier);
		}
		
		$uDb->getWebProfile()->fromArray($formUser->getWebProfile()->toArray());
		
	    }
	    $this->editorTypeHandle($uDb->getWebProfile());
	    
	    $formUser->getWebProfile()->setUpdated($now);
	    $formUser->setCreated($uDb->getCreated());
	    $formUser->setUpdated($now);
	    $formUser->setLastLogin($uDb->getLastLogin());
	    $formUser->setContact($uDb->getContact());

	    $uDb->fromArray($formUser->toArray());
	    $this->entityManager->merge($uDb);
	    $this->entityManager->flush();
	} catch (DuplicateEntryException $e) {
	    $this->imageService->removeResource($identifier);
	    $this->entityManager->rollback();
	    throw new Exceptions\DuplicateEntryException($e->getMessage(), $e->getCode(), $e->getPrevious());
	} catch (\Exception $e) {
	    $this->imageService->removeResource($identifier);
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
		$imageId = $db->getWebProfile()->getPicture();
		$this->imageService->removeResource($imageId);
		
		$this->userDao->delete($db);
		
	    } else {
		throw new EntityNotFoundException("User with id '$id' does not exist", 2);
	    }
	    $this->entityManager->commit();
	    $this->onDelete($db);
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
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given");

	try {
	    $this->entityManager->beginTransaction();
	    $user = $this->getUser($id, false);
	    
	    $newPw = Strings::random(self::RANDOM_PASS_LENGTH);
	    $user->setPassword($this->generateNewPassword($newPw));
	    $user->setPasswordChangeRequired(true);
	    
	    $this->entityManager->merge($user);
	    $this->entityManager->flush();
	    
	    $this->invalidateEntityCache($user);
	    $this->entityManager->commit();
	} catch (\Exception $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	$this->onPasswordRegenerate($user->insertRawPassword($newPw));
    }

    public function toggleUser($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '{$id}' given");
	    $active = null;
	try {
	    $this->entityManager->beginTransaction();
	    $user = $this->getUser($id, false);
	    $active = $user->getActive();
	    $user->setActive($result = !$active);
	    $this->entityManager->merge($user);
	    $this->entityManager->flush();
	    $this->invalidateEntityCache($user);
	    $this->entityManager->commit();
	} catch (\Exception $e) {
	    $this->entityManager->rollback();
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
	if (!$active) {
	    $this->onActivate($user);
	} else {
	    $this->onDeactivate($user);
	}
	
    }

    public function permitWebProfile($id, User $u) {
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
		$this->onUpdate($wpDb);
	    }
	} catch (\Exception $e) {
	    throw new Exceptions\DataErrorException($e->getMessage(), $e->getCode(), $e->getPrevious());
	}
    }

    public function denyWebProfile($id, User $u) {
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
		$this->onUpdate($wpDb);
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
