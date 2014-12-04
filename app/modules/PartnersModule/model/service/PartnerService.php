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

namespace App\PartnersModule\Model\Service;

use \App\PartnersModule\Model\Service\IPartnerService,
    \App\UsersModule\Model\Service\IUserService,
    \Kdyby\Doctrine\EntityManager,
    \Kdyby\Monolog\Logger,
    \Nette\Caching\Cache,
    \Nette\Utils\DateTime,
    \App\Model\Entities\Partner,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \App\Model\Misc\Exceptions,
    \Grido\DataSources\Doctrine,
    \Tomaj\Image\ImageService;

/**
 * Implementatiton of IPartnerService
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class PartnerService extends BaseService implements IPartnerService {
    
    const
	    ACTIVE_COLLECTION = "activePartners";
    
    /**
     * @var \Kdyby\Doctrine\EntityDao 
     */
    private $partnerDao;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    /**
     * @var \Tomaj\Image\ImageService
     */
    private $imageService;
    
    /** @var Event dispatched every time after create of Partner */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of Partner */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of Partner */
    public $onDelete = []; 
    
    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, Partner::getClassName(), $logger);
	$this->partnerDao = $em->getDao(Partner::getClassName());
    }
    
    public function setImageService(ImageService $imageService) {
	$this->imageService = $imageService;
    }

    public function getUserService() {
	if (!isset($this->userService))
	    throw new Exceptions\InvalidStateException("Property userService is not set");
	return $this->userService;
    }

    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }
    
    public function createPartner(Partner $p) {
	try {
	    $p->setUpdated(new DateTime());
	    $this->editorTypeHandle($p);
	    $this->referrerTypeHandle($p);
	    
	    $identifier = $this->imageService
		    ->storeNetteFile($p->getPicture());
	    $p->setPicture($identifier);
	    
	    $this->partnerDao->save($p);
	    $this->invalidateEntityCache($p);
	    
	    $this->onCreate($p);
	} catch (\Kdyby\Doctrine\DuplicateEntryException $ex) {
	    $this->logWarning($ex->getMessage());
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deletePartner($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	    $db = $this->partnerDao->find($id);
	    if ($db !== null) {
		$this->invalidateEntityCache($db);
		$this->partnerDao->delete($db);
		$this->onDelete($db);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getActivePartners() {
	try {
	    $qb = $this->partnerDao->createQueryBuilder("p")
		    ->where("p.active = true")
		    ->orderBy("p.name", "ASC");
	    $cache = $this->getEntityCache();
	    $data = $cache->load(self::ACTIVE_COLLECTION);
	    if ($data === null) {
		$data = $qb->getQuery()->getResult();
		$opts = [Cache::TAGS=>[self::ACTIVE_COLLECTION, self::ENTITY_COLLECTION]];
		$cache->save(self::ACTIVE_COLLECTION, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	
    }

    public function getPartner($id, $useCache = true) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	    if (!$useCache) {
		return $this->partnerDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->partnerDao->find($id);
		$opts = [Cache::TAGS=>[self::ENTITY_COLLECTION, $id, self::SELECT_COLLECTION]];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getPartnersDatasource() {
	$model = new Doctrine(
		$this->partnerDao->createQueryBuilder("p"));
	return $model;
    }

    public function updatePartner(Partner $p) {
	try {
	    $db = $this->partnerDao->find($p->getId());
	    if ($db !== null) {
		$this->partnerPictureHandle($p, $db);
		
		$db->fromArray($p->toArray());
		$db->setUpdated(new DateTime());
		$this->editorTypeHandle($db);
		$this->referrerTypeHandle($db);
		
		$this->entityManager->merge($db);
		$this->entityManager->flush();
		
		$this->invalidateEntityCache($db);
		$this->onUpdate($db);
	    }
	} catch (\Kdyby\Doctrine\DuplicateEntryException $ex) {
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function editorTypeHandle(BaseEntity $t) {
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
    
    private function referrerTypeHandle(BaseEntity $t) {
	try {
	    $r = $this->getMixId($t->getReferrer());
	    if ($r !== null) {
		$ref = $this->userService->getUser($r, false);
		$t->setReferrer($ref);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $t;
    }
    
    private function partnerPictureHandle(Partner $incoming, Partner $database) {
	if ($incoming->getPicture() == '') {
	    $incoming->setPicture($database->getPicture());
	} else {
	    $oldImageStorage = $database->getPicture();
	    $this->imageService->removeResource($oldImageStorage);
	    $identifier = $this->imageService->storeNetteFile($incoming->getPicture());
	    $incoming->setPicture($identifier);
	}
    }
}
