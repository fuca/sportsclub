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

namespace App\SystemModule\Config;

use \Nette\DI\CompilerExtension,
    \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \Kdyby\Translation\DI\ITranslationProvider,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\ICommonMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider;

/**
 * SystemModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class SystemModuleExtension extends BaseModuleExtension implements ITranslationProvider, IAdminMenuDataProvider {

    private $defaults = [];

    public function loadConfiguration() {
	parent::loadConfiguration();
	$builder = $this->getContainerBuilder();
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
    }

    public function getTranslationResources() {
	return [__DIR__ . "/../".self::LOCALE_DIR];
    }

    public function beforeCompile() {
	parent::beforeCompile();
	
	$builder = $this->getContainerBuilder();
	
	foreach ($this->compiler->getExtensions() as $extension) {
	    
			if ($extension instanceof IAdminMenuDataProvider) {
	    		    $adminFact = $builder->getDefinition($this->prefix("adminMenuControlFactory"));
			    $dataArray = $extension->getAdminItemsResources();
			    
			    foreach($dataArray as $item) {
				$adminFact->addSetup("addItem", [$item]);
			    }
			}
			
			if ($extension instanceof IProtectedMenuDataProvider) {
	    		    $protFact = $builder->getDefinition($this->prefix("protectedMenuControlFactory"));
			    $dataArray = $extension->getProtectedItemsResources();
			    
			    foreach($dataArray as $item) {
				$protFact->addSetup("addItem", [$item]);
			    }
			}
			
			if ($extension instanceof ICommonMenuDataProvider) {
	    		    $publicFact = $builder->getDefinition($this->prefix("commonMenuControlFactory"));
			    $dataArray = $extension->getCommonItemsResources();
			    
			    foreach($dataArray as $item) {
				$publicFact->addSetup("addItem", [$item]);
			    }
			}
		}
    }

    public function afterCompile(ClassType $class) {
	parent::afterCompile($class);
    }

    public function getAdminItemsResources() {
	$i = new \App\SystemModule\Model\Service\Menu\ItemData();
	$i->setLabel("systemModule.adminMenuItem.label");
	$i->setUrl(":System:Admin:default");
	return [$i];
    }

}
