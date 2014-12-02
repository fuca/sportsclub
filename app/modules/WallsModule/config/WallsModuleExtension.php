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

namespace App\WallsModule\Config;

use \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \Kdyby\Translation\DI\ITranslationProvider,
    \App\SystemModule\Model\Service\Menu\ItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\ICommonMenuDataProvider,
    \Kdyby\Doctrine\DI\IDatabaseTypeProvider;

/**
 * WallsModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class WallsModuleExtension extends BaseModuleExtension implements 
ITranslationProvider, IAdminMenuDataProvider, IProtectedMenuDataProvider, ICommonMenuDataProvider, IDatabaseTypeProvider {

    private $defaults = [];

    public function loadConfiguration() {
	parent::loadConfiguration();

	$config = $this->getConfig($this->defaults);

	$builder = $this->getContainerBuilder();

	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
    }

    public function getTranslationResources() {
	return [__DIR__ . "/../".self::LOCALE_DIR];
    }

    public function beforeCompile() {
	parent::beforeCompile();
    }

    public function afterCompile(ClassType $class) {
	parent::afterCompile($class);
    }

    public function getAdminItemsResources() {
	$i = new ItemData();
	$i->setLabel("wallsModule.adminMenuItem.label");
	$i->setUrl(":Walls:Admin:default");
	$i->setData(["desc"=>"wallsModule.adminMenuItem.description"]);
	return [$i];
    }

    public function getProtectedItemsResources() {
	return [];
    }
    
    public function getCommonItemsResources() {
	$i = new ItemData();
	$i->setLabel("wallsModule.protectedMenuItem.label");
	$i->setUrl(":Walls:Protected:default");
	$i->setData(["desc"=>"wallsModule.protectedMenuItem.description"]);
	return [$i];
    }
    
    public function getDatabaseTypes() {
	return ["WallPostStatus"=>"App\Model\Misc\Enum\WallPostStatus"];
    }

}
