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

use \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \App\Model\Misc\Exceptions,
    \Kdyby\Translation\DI\ITranslationProvider,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\ICommonMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IProtectedMenuDataProvider,
    \App\SystemModule\Model\Service\Menu\IPublicMenuDataProvider,
    \Kdyby\Doctrine\DI\IDatabaseTypeProvider;
   

/**
 * SystemModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class SystemModuleExtension extends BaseModuleExtension implements 
ITranslationProvider, IAdminMenuDataProvider, IDatabaseTypeProvider {

    private $defaults = [
	"init"=>[
	    "group"=>[
			"name"		=> "Club",
			"description"	=> "Root system group"
			]],
	"notifications"=>[
			"hostName"	    => "SPORTS CLUB IS",
			"smtpOptions"	    => [],
			"desiredMailerType" => \App\SystemModule\Model\Service\EmailNotificationService::MAILER_TYPE_SEND]
    ];

    public function loadConfiguration() {
	parent::loadConfiguration();
	
	$config = $this->getConfig($this->defaults);
	
	$builder = $this->getContainerBuilder();
	
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	
	// EMAIL NOTIFICATION SERVICE SETUP
	$notifService = $builder->getDefinition($this->prefix("notificationService"));
	$notifService->addSetup("setHostName", [$config["notifications"]["hostName"]]);
	$notifService->addSetup("setSenderEmail", [$config["notifications"]["senderMail"]]);
	$notifService->addSetup("setSmtpOptions", [$config["notifications"]["smtpOptions"]]);
	$notifService->addSetup("setDesiredMailerType", [$config["notifications"]["desiredMailerType"]]);
	
	// INITIALIZER SETUP
	$initializer = $builder->getDefinition($this->prefix("initializer"));
	$gVals = ["name"	=> $config["init"]["group"]["name"],
		  "description"	=> $config["init"]["group"]["description"],
		  "abbr"	=> "root",
		  "priority"	=>  \App\Model\Service\BaseService::MAX_PRIORITY,
		  "activity"    => true];
	$initializer->addSetup("setGroupValues", [$gVals])
		    ->addSetup("groupInit");
	
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
			
			if ($extension instanceof IPublicMenuDataProvider) {
	    		    $adminFact = $builder->getDefinition($this->prefix("publicMenuControlFactory"));
			    $dataArray = $extension->getPublicItemsResources();
			    
			    foreach($dataArray as $item) {
				$adminFact->addSetup("addItem", [$item]);
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
	$i->setData(["desc"=>"systemModule.adminMenuItem.description"]);
	return [$i];
    }
    
    public function getDatabaseTypes() {
	return ["StaticPageStatus"  => "App\Model\Misc\Enum\StaticPageStatus",
		"FormMode"	    => "App\Model\Misc\Enum\FormMode",
		"CommentMode"	    => "App\Model\Misc\Enum\CommentMode"];
    }

}
