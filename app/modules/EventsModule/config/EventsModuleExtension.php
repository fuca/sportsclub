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

namespace App\EventsModule\Config;

use \Nette\DI\CompilerExtension,
    \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \App\SystemModule\Model\Service\Menu\ItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\ICommonMenuDataProvider,
    \Kdyby\Translation\DI\ITranslationProvider,
    \Kdyby\Doctrine\DI\IDatabaseTypeProvider;

/**
 * EventsModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class EventsModuleExtension extends BaseModuleExtension implements 
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
	$i->setLabel("eventsModule.adminMenuItem.label");
	$i->setUrl(":Events:Admin:default");
	$i->setData(["desc"=>"eventsModule.adminMenuItem.description"]);
	return [$i];
    }

    public function getProtectedItemsResources() {
	$i = new ItemData();
	$i->setLabel("eventsModule.userMenuItem.label");
	$i->setUrl(":Events:User:default");
	$i->setData(["desc"=>"eventsModule.userMenuItem.description"]);
	return [$i];
    }

    public function getCommonItemsResources() {
	$i = new ItemData();
	$i->setLabel("eventsModule.clubMenuItem.label");
	$i->setUrl(":Events:Club:default");
	$i->setData(["desc"=>"eventsModule.clubMenuItem.description"]);
	return [$i];
    }
    
    public function getDatabaseTypes() {
	return ["EventParticipationType"    =>	"App\Model\Misc\Enum\EventParticipationType",
		"EventVisibility"	=>	"App\Model\Misc\Enum\EventVisibility",
		"EventType"	=>  "App\Model\Misc\Enum\EventType"];
    }
}
