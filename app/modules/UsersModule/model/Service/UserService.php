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

namespace App\Model\Service;

use \App\Services\Exceptions\NullPointerException,
    \App\Model\Service\IUserService,
    \App\Model\Entities\User,
    \App\Model\Entities\Address,
    \Kdyby\Doctrine\EntityManager,
    \Doctrine\ORM\NoResultException,
    \Kdyby\Doctrine\DBALException,
    \App\Model\Service\BaseService,
    Kdyby\Doctrine\DuplicateEntryException,
    \Nette\InvalidArgumentException,
    \App\Model\Misc\Exceptions\EntityNotFoundException,
    \App\Model\Misc\Exceptions,
    \Nette\DateTime,
    \Nette\Caching\Cache,
    \Grido\DataSources\Doctrine,
    \App\Services\Exceptions\DataErrorException;

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

    public function __construct(EntityManager $em) {
	parent::__construct($em, User::getClassName());
	$this->userDao = $em->getDao(\App\Model\Entities\User::getClassName());
	$this->addressDao = $em->getDao(\App\Model\Entities\Address::getClassName());
	$this->contactDao = $em->getDao(\App\Model\Entities\Contact::getClassName());
	$this->webProfileDao = $em->getDao(\App\Model\Entities\WebProfile::getClassName());
    }

    public function createUser(User $user) {
	if ($user == null)
	    throw new NullPointerException("Argument User cannot be null", 0);

	$this->getEntityManager()->beginTransaction();

	$now = new DateTime();
	$user->setCreated($now);
	$user->contact->setUpdated($now);
	$user->getWebProfile()->setUpdated($now);

	try {
	    $this->contactDao->save($user->getContact());
	} catch (DuplicateEntryException $e) {
	    throw new DataErrorException($e->getMessage(), 21, $e);
	}

	try {
	    $this->userDao->save($user);
	} catch (DuplicateEntryException $e) {
	    throw new DataErrorException($e->getMessage(), 22, $e);
	} catch (\Exception $e) {
// TODO
	    dd($e);
	}
	$this->getEntityManager()->commit();
    }

    public function updateUser(User $formUser) {
	if ($formUser == null)
	    throw new NullPointerException("Argument User cannot be null", 0);
	$this->getEntityManager()->beginTransaction();

	$uDb = $this->getUserId($formUser->id);
	if ($uDb !== null) {
	    
	    try {
		$uDbContact = $uDb->getContact();
		
		$formUser->getContact()->setUpdated(new DateTime());
		$formUser->getContact()->setId($uDbContact->getId());
		
		$this->getEntityManager()->merge($formUser->getContact());
		$this->getEntityManager()->flush();
	    } catch (DuplicateEntryException $e) {
		//dd([21,$e]);
		throw new DataErrorException($e->getMessage(), 21, $e);
	    }

	    try {
		$formUser->setWebProfile($uDb->getWebProfile());
		$formUser->setCreated($uDb->getCreated());
		$formUser->setLastLogin($uDb->getLastLogin());
		$uArray = $formUser->toArray();
		$uDb->fromArray($uArray);
		$this->getEntityManager()->merge($uDb);
		$this->getEntityManager()->flush();
	    } catch (DuplicateEntryException $e) {
		//dd([22,$e]);
		throw new DataErrorException($e->getMessage(), 22, $e);
	    } catch (\Exception $e) {
		// TODO
		dd(["UserService 136", $e]);
	    }
	}
	$this->entityManager->commit();
    }

    public function deleteUser($id) {
	if (!is_numeric($id))
	    throw new InvalidArgumentException("Argument Id must be type of numeric, '$id' given", 1);

	$this->getEntityManager()->beginTransaction();
	try {
	$db = $this->getUser($id);
	if ($db !== null) {
	    $this->userDao->delete($db);
//	    $count = $this->getAddressReferencesCount($db->contact->address);
//	    if ($count <= 1) {
//		$this->addressDao->delete($db->getContact()->getAddress());
//	    }
	} else {
	    throw new EntityNotFoundException("User with id '$id' does not exist", 2);
	}
	$this->getEntityManager()->commit();
	} catch (DBALException $ex) {
	    throw new Exceptions\DependencyException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex);
	}
    }

//    private function getAddressReferencesCount(Address $a) {
//	$qb = $this->addressDao->createQueryBuilder();
//	$qb->select("COUNT(c.id)")
//		->from("App\Model\Entities\Contact", "c")
//		->where("c.address = :id")
//		->setParameter("id", $a->id);
//	return $qb->getQuery()->getSingleScalarResult();
//    }

    public function getUser($id) {
	if ($id == NULL)
	    throw new NullPointerException("Argument Id cannot be null", 0);
	if (!is_numeric($id))
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, '$id' given", 1);
	try {
	    return $this->userDao->find($id);    
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	
    }

    public function getUsers() {
	try {
	    return $this->userDao->findAll();
	} catch(Exception $ex) {
	    throw new DataErrorException($ex);
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
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
	
    }

    public function getSelectUsers($id = null) {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	try {
	    if ($data === null) {
		$data = $this->userDao->findPairs([], 'surname'); // TODO CONCAT
		$opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
		$cache->save(self::SELECT_COLLECTION, $data, $opt);
	    }
	    if ($id != null) {
		unset($data[$id]);
	    }
	    return $data;
	} catch (\Exception $e) {
	    // TODO LOG
	    dd($e);
	}
    }

}
