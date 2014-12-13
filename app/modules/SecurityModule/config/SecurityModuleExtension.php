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
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IPublicMenuDataProvider,
    \Kdyby\Doctrine\DI\IDatabaseTypeProvider;

/**
 * SecurityModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class SecurityModuleExtension extends BaseModuleExtension implements 
ITranslationProvider, IAdminMenuDataProvider, IProtectedMenuDataProvider, IPublicMenuDataProvider, IDatabaseTypeProvider {

    private $defaults = [
	"defRoleAppEvents"	=> "player", 
	"defCommentAppEvents"	=> "created by system",
	"deleteOldPositions"	=> false,
	    "init"  =>	[
		"turnOff"=>false,
		"roles"	=>  ["admin", "player"]]];
		    

    public function loadConfiguration() {
	parent::loadConfiguration();

	$config = $this->getConfig($this->defaults);

	$builder = $this->getContainerBuilder();

	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	
	// Listener of Season application's events setup
	$builder->getDefinition($this->prefix("applicationsListener"))
		->addSetup("setDefaultRoleName", [$config["defRoleAppEvents"]])
		->addSetup("setDefaultComment", [$config["defCommentAppEvents"]]);
	
	
	$rValues = array_unique(array_merge($config["init"]["roles"], [$config["defRoleAppEvents"]], ["admin","authenticated"]));
	$initializer = $builder->getDefinition($this->prefix("initializer"))
		->addSetup("setRolesValues", [$rValues])
		->addSetup("setDefaultUserEmail", [$config["defaultUserEmail"]]);
	
	if (!$config["init"]["turnOff"])
	    $initializer->addSetup("rolesInit")
		->addSetup("positionsInit")
		->addSetup("rulesInit");
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
	$i->setData(["desc"=>"securityModule.adminMenuItem.description"]);
	return [$i];
    }
    
    public function getPublicItemsResources() {
	$i = new ItemData();
	$i->setLabel("securityModule.public.menu.contacts.label");
	$i->setUrl(":Security:Public:default");
	$i->setData(["desc"=>"securityModule.public.menu.contacts.description"]);
	return [$i];
    }

    public function getProtectedItemsResources() {
	$i = new ItemData();
	$i->setLabel("securityModule.protectedMenuItem.label");
	$i->setUrl(":Security:Auth:out");
	$i->setData(["separate"=>true,"headOnly"=>true, "desc"=>"securityModule.protectedMenuItem.description"]);
	return [$i];
    }
    
    public function getDatabaseTypes() {
	return ["AclMode"   =>	"App\Model\Misc\Enum\AclMode",
		"AclPrivilege"	=>  "App\Model\Misc\Enum\AclPrivilege"];
    }
}