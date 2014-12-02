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

namespace App\ForumModule\Config;

use \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \App\Model\Misc\Exceptions,
    \Nette\Utils\FileSystem,
    \App\SystemModule\Model\Service\Menu\ItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\ICommonMenuDataProvider,
    \Kdyby\Translation\DI\ITranslationProvider,
    \Kdyby\Doctrine\DI\IDatabaseTypeProvider;

/**
 * ForumModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class CommunicationModuleExtension extends BaseModuleExtension implements ITranslationProvider, IAdminMenuDataProvider, IProtectedMenuDataProvider, ICommonMenuDataProvider, IDatabaseTypeProvider {

    private $defaults = ["defaultImg" => "default-forum-ico.png"];

    public function loadConfiguration() {
	parent::loadConfiguration();

	$config = $this->getConfig($this->defaults);

	$builder = $this->getContainerBuilder();

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
	$i->setLabel("communicationModule.adminMenuItem.label");
	$i->setUrl(":Communication:Admin:default");
	$i->setData(["desc"=>"communicationModule.adminMenuItem.description"]);
	return [$i];
    }

    public function getProtectedItemsResources() {
	$y = new ItemData();
	$y->setLabel("communicationModule.protectedMessagesMenuItem.label");
	$y->setUrl(":Communication:Messaging:default");
	$y->setData(["desc"=>"communicationModule.protectedMessagesMenuItem.description"]);
	return [$y];
    }

    public function getCommonItemsResources() {
	$i = new ItemData();
	$i->setLabel("communicationModule.protectedForumMenuItem.label");
	$i->setUrl(":Communication:Forum:default");
	$i->setData(["desc"=>"communicationModule.protectedForumMenuItem.description"]);
	return [$i];
    }
    
    public function getDatabaseTypes() {
	return ["MailBoxEntryType"  =>	"App\Model\Misc\Enum\MailBoxEntryType"];
    }
}