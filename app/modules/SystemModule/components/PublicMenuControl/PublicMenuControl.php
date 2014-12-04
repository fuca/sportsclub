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

use \App\Model\Misc\Exceptions,
    \Nette\ComponentModel\IContainer,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\SystemModule\Model\Service\IStaticPageService,
    \App\SystemModule\Model\Service\ISportTypeService,
    \Doctrine\Common\Collections\ArrayCollection;

/**
 * PublicMenuControl
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 * @package sportsclub
 */
final class PublicMenuControl extends \Nette\Application\UI\Control {
    
    const   
	TYPE_ID	    = "type",
	GROUPS_ID   = "groups",
	ROOT_ID	    = "root";

    /**
     * @var string directory with templates
     */
    private $templatesDir;

    /** @var string template file */
    private $templateFile;

    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService 
     */
    //private $sportGroupService;

    /**
     * @var \App\SystemModule\Model\Service\ISportTypeService
     */
    //private $sportTypeService;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $brands;
    
    /**
     * @var [["self::TYPE_ID"=>\App\Model\Misc\SportType, 
     "self::GROUPS_ID"=>\Doctrine\Common\Collections\ArrayCollection, 
     "self::ROOT_ID"=>\App\Model\Misc\SportGroup]]
     */
    private $treeData;

    public function __construct(IContainer $parent, $name, 
	    ISportGroupService $groupService, 
	    ISportTypeService $typeService) {
	
	parent::__construct($parent, $name);
	//$this->sportGroupService    = $groupService;
	//$this->sportTypeService	    = $typeService;
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

    public function setBrands(ArrayCollection $collectedData) {
	$this->brands = $collectedData;
    }
    
    /**
     * @var [["self::TYPE_ID"=>\App\Model\Misc\SportType, 
     "self::GROUPS_ID"=>\Doctrine\Common\Collections\ArrayCollection, 
     "self::ROOT_ID"=>\App\Model\Misc\SportGroup]]
     * @throws Exceptions\InvalidArgumentException
     */
    public function setTreeData(array $array) {
	foreach ($array as $a) {
	if (!isset($a[self::TYPE_ID]) ||
	    !isset($a[self::GROUPS_ID]) ||
	    !isset($a[self::ROOT_ID]))
		throw new Exceptions\InvalidArgumentException("Passed array does not contain all required items");
	}
	$this->treeData = $array;
    }

    public function render() {
	$this->template->setFile($this->getTemplateFile());
	$this->template->collectedData = $this->brands;
	$this->template->sportsData = $this->treeData;
	$this->template->render();
    }

}
