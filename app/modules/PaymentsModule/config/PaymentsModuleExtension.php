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

namespace App\PaymentsModule\Config;

use \Nette\DI\CompilerExtension,
    \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \App\SystemModule\Model\Service\Menu\ItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider,
    \Kdyby\Translation\DI\ITranslationProvider;

/**
 * PaymentsModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class PaymentsModuleExtension extends BaseModuleExtension implements ITranslationProvider, IProtectedMenuDataProvider, IAdminMenuDataProvider {

    private $defaults = [
	"dueDate"=>"1 month"];

    public function loadConfiguration() {
	parent::loadConfiguration();

	$config = $this->getConfig($this->defaults);

	$builder = $this->getContainerBuilder();
	
	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	
	$builder->getDefinition($this->prefix("paymentService"))
		->addSetup("setDefaultDueDate", [$config["dueDate"]]);
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

    public function getProtectedItemsResources() {
	$i = new ItemData();
	$i->setLabel("paymentsModule.protectedMenuItem.label");
	$i->setUrl(":Payments:User:default");
	return [$i];
    }

    public function getAdminItemsResources() {
	$i = new ItemData();
	$i->setLabel("paymentsModule.adminMenuItem.label");
	$i->setUrl(":Payments:Admin:default");
	return [$i];
    }

}