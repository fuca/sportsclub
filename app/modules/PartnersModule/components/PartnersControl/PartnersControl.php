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

namespace App\SystemModule\Components;

use \Nette\Application\UI\Control,
    \Nette\ComponentModel\IContainer;

/**
 * Control for display partners section at homepage
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class PartnersControl extends Control {
     /**
     * @var string directory with templates
     */
    private $templatesDir;

    /** @var string template file */
    private $templateFile;
    
    /**
     * Array of Partner entities designated to render
     * @var array $partners
     */
    private $partners;
    
    /**
     * Class onstructor
     * @param IContainer $parent
     * @param string $name
     * @param array $data Array of Partner entities for render.
     */
    public function __construct(IContainer $parent, $name, array $data) {
	
	parent::__construct($parent, $name);
	$this->partners = $data;
	$this->templatesDir	    = __DIR__ . "/templates/";
	$this->templateFile	    = $this->templatesDir . "default.latte";
    }
    
    public function getTemplateFile() {
	return $this->templateFile;
    }

    /**
     * Sets template file name. Must be in templates directory.
     * @param string $template
     * @throws \Nette\FileNotFoundException
     */
    public function setTemplateFile($template) {
	if (!file_exists($this->templatesDir . $template))
	    throw new \Nette\FileNotFoundException("Template file with name '$template' does not exist");
	$this->templateFile = $template;
    }
    
    private function getPartners() {
	return $this->partners;
    }
    
    /**
     * Renders control template
     */
    public function render() {
	$this->template->setFile($this->getTemplateFile());
	$this->template->partners = $this->getPartners();
	$this->template->render();
    }
}
