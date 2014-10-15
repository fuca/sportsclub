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
    \Kdyby\Translation\DI\ITranslationProvider;

/**
 * ArticlesModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class ArticlesModuleExtension extends BaseModuleExtension implements ITranslationProvider {

    private $defaults = [];

    public function loadConfiguration() {
	parent::loadConfiguration();

	// nactu si konfigurace odpovidajici sekce z globalniho configu
	$config = $this->getConfig($this->defaults);

	// vytahnu si containere buildera (ten generuje kod pro konfiguraci)
	$builder = $this->getContainerBuilder();

	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	// $translator = $builder->getDefinition("translation.default"); // for example obtaining of service // 
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

}
