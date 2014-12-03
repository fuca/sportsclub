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

namespace App\ArticlesModule\Config;

use \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \App\Model\Misc\Exceptions,
    \Nette\Utils\FileSystem,
    \App\SystemModule\Model\Service\Menu\ItemData,
    \App\SystemModule\Model\Service\Menu\IAdminMenuDataProvider,
    \App\ArticlesModule\Model\Service\ArticleService,
    \Kdyby\Translation\DI\ITranslationProvider,
    \Kdyby\Doctrine\DI\IDatabaseTypeProvider,
    \App\SystemModule\Model\Service\Menu\IPublicMenuDataProvider;

/**
 * ArticlesModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class ArticlesModuleExtension extends BaseModuleExtension implements 
ITranslationProvider, IAdminMenuDataProvider, IDatabaseTypeProvider, IPublicMenuDataProvider {

    private $defaults = [
	ArticleService::DEFAULT_IMAGE_PATH  => "article",
	ArticleService::DEFAULT_THUMBNAIL   => "articleThumbDefault.png",
	ArticleService::DEFAULT_IMAGE	    => "articleImageDefault.png",
	ArticleService::DEFAULT_RSS_LIMIT   => 50];

    public function loadConfiguration() {
	parent::loadConfiguration();

	// nactu si konfigurace odpovidajici sekce z globalniho configu
	$config = $this->getConfig($this->defaults);

	// vytahnu si containere buildera (ten generuje kod pro konfiguraci)
	$builder = $this->getContainerBuilder();

	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	// $translator = $builder->getDefinition("translation.default"); // for example obtaining of service // 
	
	// Article service configuration
	$artService = $builder->getDefinition($this->prefix("articleService"));
	$artService->addSetup("setConfig", [$config]);
	
	$rssPresenter = $builder->getDefinition($this->prefix("rssPresenter"));
	$rssPresenter->addSetup("setRssPropertiesConfig", [$config["rss"]]);
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
	$i->setLabel("articlesModule.adminMenuItem.label");
	$i->setUrl(":Articles:Admin:default");
	$i->setData(["desc"=>"articlesModule.adminMenuItem.description"]);
	return [$i];
    }
    
    public function getPublicItemsResources() {
	$i = new ItemData();
	$i->setLabel("articlesModule.publicMenu.articles.label");
	$i->setUrl(":Articles:Public:default");
	$i->setData(["desc"=>"articlesModule.publicMenu.articles.description"]);
	return [$i];
    }
    
    public function getDatabaseTypes() {
	return ["ArticleStatus"=>"App\Model\Misc\Enum\ArticleStatus"];
    }
}
