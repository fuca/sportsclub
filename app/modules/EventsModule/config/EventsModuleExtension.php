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
use 
    \Nette\DI\CompilerExtension,
    \Nette\PhpGenerator\ClassType;

/**
 * EventsModuleExtension
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class EventsModuleExtension extends CompilerExtension {
    
    private $defaults = [];
    
    public function loadConfiguration() {
	parent::loadConfiguration();
	$config = $this->getConfig($this->defaults); // nactu si konfigurace odpovidajici sekce z configu
	$builder = $this->getContainerBuilder();
	
	// načtení konfiguračního souboru pro rozšíření
	$this->compiler->parseServices($builder, $this->loadFromFile(__DIR__ . '/config.neon'));
	//predd($this->loadFromFile(__DIR__ . '/config.neon'));
//	
//	if (!file_exists('path/to/directory')) {
//	    mkdir('path/to/directory', 0777, true);


//	// počet článků na stránku v komponentě
//	$builder->getDefinition($this->prefix('articlesList'))
//	    ->addSetup('setPostsPerPage', $config['postsPerPage']);
//
//	// volitelné vypnutí komentářů
//	if (!$config['comments']) {
//	    $builder->getDefinition($this->prefix('comments'))
//		->addSetup('disableComments');
//	}
    }
    
    public function beforeCompile() {
	/*
	 * V této fázi sestavování už by neměly přibývat další služby. 
	 * Můžeme ovšem upravovat existující a doplnit některé potřebné vazby 
	 * mezi službami, například pomocí tagů.
	 */
    }
    
    public function afterCompile(ClassType $class) {
	parent::afterCompile($class);
    }
    
}