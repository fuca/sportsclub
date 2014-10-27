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

/*
 * Cache se vytvari podle namespace servicy
 * klice k jednotlivym entitam jsou jejich id a konstanty v teto tride
 */

namespace App\Model\Service;

use 
    \Nette\Utils\Strings,
    \Kdyby\Doctrine\Entities\BaseEntity,
    \Nette\Caching\Cache,
    \Nette\Object,
    \App\Model\Misc\Exceptions,
    \Nette\Caching\IStorage,	
    \App\Model\IIdentifiable,
    \Kdyby\Doctrine\EntityManager,
    \Kdyby\Monolog\Logger;

/**
 * Abstract parent of services
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
abstract class BaseService extends Object {
    
    const ENTITY_COLLECTION = "collection",
	  STRANGER_COLLECTION = "stranger",
	  SELECT_COLLECTION = "selectList";
    
    const MAX_PRIORITY = 10;
    
    /**
     * @var \Kdyby\Doctrine\EntityManager
     */
    protected $entityManager;
    
    /**
     * @var \Nette\Caching\IStorage
     */
    private $cacheStorage;
    
    /**
     * @var \Nette\Caching\Cache
     */
    private $entityCache;
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    protected $logger;
    
    private $className;
    
    private $entityClassName;
    
    private $onCreate;
    
    private $onDelete;
    
    private $onUpdate;
    
    protected function getCacheStorage() {
	return $this->cacheStorage;
    }
        
    public function setEntityClassName($entityClassName) {
	$this->entityClassName = $entityClassName;
    }

    public function getClassName() {
	if (!isset($this->className))
	    $this->className = self::getReflection()->name;
	return $this->className;
    }
    
    public function getEntityClassName() {
	return $this->entityClassName;
    }
        
    public function setCacheStorage(IStorage $cacheStorage) {
	$this->cacheStorage = $cacheStorage;
    }
    
    public function setLogger(Logger $logger) {
	$this->logger = $logger;
    }
    
    protected function getLogger() {
	if (!isset($this->logger))
	    throw new Exceptions\InvalidStateException("Property Logger is not correctly set, please use appropriate setter first");
	return $this->logger;
    }
    
    protected function getMixId($o) {
	if ($o === null) return null;
	return $o instanceof IIdentifiable ? $o->getId() : $o;
    }
    
    /**
     * @return \Nette\Caching\Cache
     */
    protected function getEntityCache() {
	if (!isset($this->entityCache)) {
	    $this->entityCache = new Cache(
		    $this->cacheStorage, 
		    Strings::fixEncoding($this->getClassName() . "/" . $this->getEntityClassName()));
	}
	return $this->entityCache;
    }
    
    protected function __construct(EntityManager $em, $entityClassName, Logger $logger = null) {
	if ($logger !== null) $this->logger = $logger;
	$this->entityManager = $em;
	$this->setEntityClassName($entityClassName);
    }
    
    protected function invalidateEntityCache(BaseEntity $e = null, array $tags = [self::ENTITY_COLLECTION, self::SELECT_COLLECTION], $force = false) {
	$cache = $this->getEntityCache();
	if ($e === null) {
	    if ($force) {
		$this->purgeCache();
		return;
	    }
	    $cache->clean([Cache::TAGS => $tags]);
	} else {
	    $tags[] = $e->getId();
	    $cache->clean([Cache::TAGS => $tags]);
	}
    }
    
    /**
     * Deletes whole storage cache, not only namespace (Nette bug)
     */
    protected function purgeCache() {
	$this->getEntityCache()->clean([Cache::ALL=>true]);
    }
    
    // <editor-fold desc="LOGGING SUPPORT"> 
    
    private function prefixMessage($message, $type) {
	return "###   ".$type."   ### ".$this->className." -->  \n".$message;
    }
    
    protected function logError($message, array $context = []) {
	$this->logger->addError($this->prefixMessage($message, "ERROR"), $context);
    }
    
    protected function logWarning($message, array $context = []) {
	$this->logger->addWarning($this->prefixMessage($message, "WARNING"), $context);
    }
    
    protected function logInfo($message, array $context = []) {
	$this->logger->addInfo($this->prefixMessage($message, "INFO"), $context);
    }
    
    protected function logDebug($message, array $context = []) {
	$this->logger->addDebug($this->prefixMessage($message, "DEBUG"), $context);
    }
    //</editor-fold>
    
}
