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

namespace App\UsersModule\Config;

use \Nette\DI\CompilerExtension,
    \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \Kdyby\Translation\DI\ITranslationProvider,
    \App\SystemModule\Model\Service\Menu\ItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider,
    \Kdyby\Doctrine\DI\IDatabaseTypeProvider;

/**
 * UsersModule compiler extension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class UsersModuleExtension extends BaseModuleExtension implements 
ITranslationProvider, IAdminMenuDataProvider, IProtectedMenuDataProvider, IDatabaseTypeProvider {

     private $defaults = [
	"init"=>[
		"turnOff"=>false,
		"user"=>[ // this configuration is here only for demonstration
			"name"=>"FBC",
			"surname"=>"Mohelnice, o.s.",
			"nick"=>"Informační systém",
			"password"=>"admin",
			"contact"=>[
				"address"=>[
					"city"=>"Mohelnice",
					"postCode"=>"789 85",
					"street"=>"Masarykova",
					"number"=>"546/25",
					"accountNumber"=>"2500140367/2010",
					"in"=>"",
					"tin"=>""],
				"phone"=>"420732504156",
				"email"=>"michal.fuca.fucik@gmail.com"]]],
    ];

    public function loadConfiguration() {
	parent::loadConfiguration();

	$config = $this->getConfig($this->defaults);

	$builder = $this->getContainerBuilder();

	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	
	// Initializer setup
	$initializer = $builder->getDefinition($this->prefix("initializer"));
	$initializer->addSetup("setUserValues", [$config["init"]["user"]]);
	if (!$config["init"]["turnOff"])
	    $initializer->addSetup("userInit");
	
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
	$i->setLabel("usersModule.adminMenuItem.label");
	$i->setUrl(":Users:Admin:default");
	$i->setData(["desc"=>"usersModule.adminMenuItem.description"]);
	return [$i];
    }

    public function getProtectedItemsResources() {
	$i = new ItemData();
	$i->setLabel("usersModule.protectedMenuDataItem.label");
	$i->setUrl(":Users:User:data");
	$i->setData(["desc"=>"usersModule.protectedMenuDataItem.description"]);
	
	$y = new ItemData();
	$y->setLabel("usersModule.protectedMenuProfileItem.label");
	$y->setUrl(":Users:User:profile");
	$y->setData(["desc"=>"usersModule.protectedMenuProfileItem.description"]);
	return [$i, $y];
    }
    
    public function getDatabaseTypes() {
	return ["WebProfileStatus"  =>	"App\Model\Misc\Enum\WebProfileStatus",
		"CommentMode"	    =>	"App\Model\Misc\Enum\CommentMode"];
    }
}
