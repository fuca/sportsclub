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

namespace App\SeasonsModule\Components;

use \Nette\Application\UI\Control,
    \Nette\ComponentModel\IContainer,
    \App\Model\Entities\Seasonm
    \App\Model\Misc\Enum\WebProfileStatus;

/**
 * Control for displaying information about season
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class SeasonInfoControl extends Control {
    
    /**
     * @var string directory with templates
     */
    private $templatesDir;

    /** @var string template file */
    private $templateFile;
    
    /**
     * Actual season
     * @var Season
     */
    private $season;
    
    public function __construct(IContainer $parent, $name) {
	parent::__construct($parent, $name);
	$this->templatesDir	    = __DIR__ . "/templates/";
	$this->templateFile	    = $this->templatesDir . "default.latte";
    }
    
    public function getTemplateFile() {
	return $this->templateFile;
    }

    public function setTemplateFile($template) {
	if (!file_exists($this->templatesDir . $template))
	    throw new \Nette\FileNotFoundException("Template file with name '$template' does not exist");
	$this->templateFile = $template;
    }
    
    public function getSeason() {
	return $this->season;
    }

    public function setSeason($season) {
	$this->season = $season;
    }
    
    /**
     * Template render
     */
    public function render() {
	$this->template->setFile($this->getTemplateFile());
	$this->template->season = $this->getSeason();
	$this->template->render();
    }
}