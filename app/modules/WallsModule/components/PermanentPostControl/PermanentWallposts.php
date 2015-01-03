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

namespace App\WallsModule\Components;

use \Nette\Application\UI\Control,
    \Nette\ComponentModel\IContainer;

/**
 * Permanent wallposts control
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class PermanentWallposts extends Control {
    
    /**
     * @var string directory with templates
     */
    private $templatesDir;

    /** @var string template file */
    private $templateFile;
    
    /**
     * Data for render
     * @var [WallPost]
     */
    private $data;
    
    /**
     * Param to pass into link
     * @var string
     */
    private $param;
    
    /**
     * Class constructor
     * @param IContainer $parent
     * @param string $name
     */
    public function __construct(IContainer $parent, $name) {
	parent::__construct($parent, $name);
	$this->templatesDir = __DIR__ . "/templates/";
	$this->templateFile = $this->templatesDir . "default.latte";
	$this->data	    = [];
    }
    
    public function getTemplateFile() {
	return $this->templateFile;
    }

    /**
     * Template file path setter. Has to be in templates directory.
     * @param string $template
     * @throws \Nette\FileNotFoundException
     */
    public function setTemplateFile($template) {
	if (!file_exists($this->templatesDir . $template))
	    throw new \Nette\FileNotFoundException("Template file with name '$template' does not exist");
	$this->templateFile = $template;
    }
    
    public function getData() {
	return $this->data;
    }

    public function setData(array $data) {
	$this->data = $data;
    }
    
    public function setParam($string) {
	$this->param = $string;
    }

    /**
     * Template render method
     */
    public function render() {
	$this->template->setFile($this->getTemplateFile());
	$this->template->data = $this->getData();
	$this->template->param = $this->param;
	$this->template->render();
    }
}
