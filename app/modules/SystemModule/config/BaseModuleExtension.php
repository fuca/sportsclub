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

namespace App\Config;

use \Nette\DI\CompilerExtension,
    \Nette\PhpGenerator\ClassType,
    \Nette\Utils\FileSystem;

/**
 * BaseModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
abstract class BaseModuleExtension extends CompilerExtension {
    
    const   CACHE_DIR	= "cacheDir",
	    LOCALE_DIR	= "locale";
    
    public function getModuleName() {
	return $this->name;
    }

    public function loadConfiguration() {
	parent::loadConfiguration();
	
	$builder = $this->getContainerBuilder();
	
	$cacheDir = $builder->parameters[self::CACHE_DIR]; 
	$servicesCacheDir = $cacheDir."services";
	$moduleCacheDir = $servicesCacheDir."/".$this->getModuleName();
	
	if (!file_exists($servicesCacheDir)) {
	    try {
		FileSystem::createDir($servicesCacheDir);
	    } catch (Nette\IOException $ex) {
		throw new Exceptions\InsufficientPermissionException("Permission denied while creating $servicesCacheDir directory");
	    }
	}
	
	if (!file_exists($moduleCacheDir)) {
	    try {
		FileSystem::createDir($moduleCacheDir);
	    } catch (Nette\IOException $ex) {
		throw new Exceptions\InsufficientPermissionException("Permission denied while creating $moduleCacheDir directory");
	    }
	}
    }
    
    public function beforeCompile() {
	parent::beforeCompile();
	
    }
    
    public function afterCompile(ClassType $class) {
	parent::afterCompile($class);
    }
}
