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
    \Nette\ComponentModel\IContainer,
    \App\Model\Entities\User,
    \App\Model\Misc\Enum\WebProfileStatus;

/**
 * Control for displaying appeal messages
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class AppealControl extends Control {
    
    /**
     * @var string directory with templates
     */
    private $templatesDir;

    /** @var string template file */
    private $templateFile;
    
    /**
     * User which is appealed on
     * @var array $partners
     */
    private $user;
    
    public function __construct(IContainer $parent, $name, User $user) {
	parent::__construct($parent, $name);
	$this->user = $user;
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
    
    /**
     * Template render
     */
    public function render() {
	$this->template->setFile($this->getTemplateFile());
	$this->template->changePassword = $this->user->getPasswordChangeRequired();
	$this->template->fillProfile = $this->user->getWebProfile()->getStatus() == WebProfileStatus::OK?false:true;
	$this->template->render();
    }
}
