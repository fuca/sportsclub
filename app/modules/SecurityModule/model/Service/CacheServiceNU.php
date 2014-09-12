<?php

namespace App\Model\Service;

/**
 * Description of CacheService
 *
 * @author fuca
 */
class CacheService extends \Nette\Object {
    
    const MAX_PRIORITY = 10;
    
    /**
     * @var \Nette\Caching\IStorage
     */
    private $cacheStorage;
    
    /**     
     * @var Nette\Caching\Cache
     */
    private $cache;
    
    /** @var string class name */
    private $className;
    
    public function getClassName() {
	if (!isset($this->className)) {
	    $this->className = self::getReflection()->name;
	}
	return $this->className;
    }
    
    protected function getCacheStorage() {
	return $this->cacheStorage;
    }
    
    public function setCacheStorage(\Nette\Caching\IStorage $cacheStorage) {
	$this->cacheStorage = $cacheStorage;
    }
    
    public function __construct() {
	$this->cacheInstances = [];
    }

    public function getCache($className) {
	if (class_exists($className)) {
	    if (!isset($this->cacheInstances[$className])) {
		$this->cacheInstances[$className] = new Nette\Caching\Cache($this->cacheStorage, Strings::fixEncoding("services/". $this->getClassName() . "/" . $className));
	    }
	    return $this->cacheInstances[$className];
	}
	return null;
    }
    
}
