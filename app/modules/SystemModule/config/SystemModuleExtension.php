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

use \Nette\DI\CompilerExtension,
    \Nette\PhpGenerator\ClassType,
    \App\Config\BaseModuleExtension,
    \Kdyby\Translation\DI\ITranslationProvider;

/**
 * SystemModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class SystemModuleExtension extends BaseModuleExtension implements ITranslationProvider {

    private $defaults = [];

    public function loadConfiguration() {
	parent::loadConfiguration();
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