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

namespace App\SecurityModule\Config;

use \Nette\DI\CompilerExtension,
    \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \App\SystemModule\Model\Service\Menu\ItemData,
    \Kdyby\Translation\DI\ITranslationProvider,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider;

/**
 * SecurityModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class SecurityModuleExtension extends BaseModuleExtension implements ITranslationProvider, IAdminMenuDataProvider, IProtectedMenuDataProvider {

    private $defaults = [
	"evDefRoleName" => "player", 
	"evDefComment" => "created by system",
	"deleteOldPositions" => false];

    public function loadConfiguration() {
	parent::loadConfiguration();

	$config = $this->getConfig($this->defaults);

	$builder = $this->getContainerBuilder();

	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	
	$builder->getDefinition($this->prefix("applicationsListener"))
		->addSetup("setDefaultRoleName", [$config["evDefRoleName"]])
		->addSetup("setDefaultComment", [$config["evDefComment"]]);
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
	$i->setLabel("securityModule.adminMenuItem.label");
	$i->setUrl(":Security:Admin:default");
	return [$i];
    }

    public function getProtectedItemsResources() {
	$i = new ItemData();
	$i->setLabel("securityModule.protectedMenuItem.label");
	$i->setUrl(":Security:Auth:out");
	$i->setData(["separate"=>true,"headOnly"=>true]);
	return [$i];
    }

}