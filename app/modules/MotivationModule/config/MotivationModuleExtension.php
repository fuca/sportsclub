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

namespace App\MotivationModule\Config;

use \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \Kdyby\Translation\DI\ITranslationProvider,
    \Doctrine\DBAL\Types\Type,
    \App\SystemModule\Model\Service\Menu\ItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider,
    \Kdyby\Doctrine\DI\IDatabaseTypeProvider;

/**
 * MotivationModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class MotivationModuleExtension extends BaseModuleExtension implements 
ITranslationProvider, IAdminMenuDataProvider, IProtectedMenuDataProvider, IDatabaseTypeProvider {

    private $defaults = [
	"dueDate"=>"1 month"];

    public function loadConfiguration() {
	parent::loadConfiguration();

	$config = $this->getConfig($this->defaults);

	$builder = $this->getContainerBuilder();
	
	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	
//	Type::addType("MotivationEntryType", "App\Model\Misc\Enum\MotivationEntryType");
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
	$i->setLabel("motivationModule.adminMenuItem.label");
	$i->setUrl(":Motivation:Admin:default");
	$i->setData(["desc"=>"motivationModule.adminMenuItem.description"]);
	return [$i];
    }

    public function getProtectedItemsResources() {
	$i = new ItemData();
	$i->setLabel("motivationModule.protectedMenuItem.label");
	$i->setUrl(":Motivation:Protected:default");
	$i->setData(["desc"=>"motivationModule.protectedMenuItem.description"]);
	return [$i];
    }
    
    public function getDatabaseTypes() {
	return ["MotivationEntryType"	=>  "App\Model\Misc\Enum\MotivationEntryType"];
    }
}