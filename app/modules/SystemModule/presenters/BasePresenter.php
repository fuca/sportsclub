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

namespace App\SystemModule\Presenters;

use \Nette,
    \Nette\InvalidArgumentException,
    \Nette\Application\UI\Presenter,
    \Grido\Components\Filters\Filter,
    \App\Model\Misc\Enum\CommentMode,
    \App\SystemModule\Model\Service\ICommentable;

/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Presenter {

    const NUM_IDENTIFIER = 'id';
    const SECURED_ANNOTATION_ID = "Secured";

    /**
     * @const string Flash messages type
     */
    const FM_SUCCESS = "success",
	    FM_ERROR = "error",
	    FM_WARNING = "warning";

    /** @var string @persistent */
    public $ajax = 'on';

    /** @persistent */
    protected $lang = 'cz';
    
    /** @vat actual managinng entity id from parameter */
    private $entityId;
    
    /** @var actual managing entity entity*/
    private $entity;

    /**
     * @inject
     * @var \Kdyby\Translation\Translator
     */
    public $translator;
    
    /**
     * @inject
     * @var \Kdyby\Monolog\Logger
     */
    public $logger;

    public function getEntityId() {
	if (!isset($this->entityId))
	    throw new Exceptions\InvalidStateException("EntityId is not set, it seems it wasn't passed as request '".self::NUM_IDENTIFIER."' parameter");
	return $this->entityId;
    }
    
    public function getEntity() {
	if (!isset($this->entity))
	    throw new Exceptions\InvalidStateException("Actual entity is not set, please use appropriate setter first");
	return $this->entity;
    }

    public function setEntity($entity) {
	$this->entity = $entity;
    }
    
    public function getTranslator() {
	return $this->translator;
    }

    /** @var string */
    public $filterRenderType = Filter::RENDER_INNER;

    public function startup() {
	parent::startup();
	$this->entityId = $this->getParameter(self::NUM_IDENTIFIER);
    }

    protected function createTemplate($class = NULL) {
	$template = parent::createTemplate($class);
	$template->registerHelperLoader(callback($this->translator->createTemplateHelpers(), 'loader'));

	return $template;
    }

    protected function beforeRender() {
	parent::beforeRender();
	$this->setLayout('publicLayout');

	$this->template->setTranslator($this->translator);
	//$this->template->layoutsPath = '../../../../templates/';
	$appDir = $this->context->parameters['appDir'];
	$this->template->layoutsPath = $appDir . "/modules/SystemModule/templates/";
    }
    
    protected function handleException( $ex) {
	dd($ex);
    }

    // <editor-fold desc="COMMON COMPONENT FACTORIES">

    public function createComponentLoginControl($name) {
	$c = new \App\SystemModule\Components\LogInControl($this, $name);
	$c->setLogInTarget(":System:Public:default");
	return $c;
    }
    
    public function createComponentCommentControl($name) {
	$c = new \App\SystemModule\Components\CommentControl($this, $name);
	$c->setEntity($this->getEntity());
	$c->setUser($this->getUser()->getIdentity());
	//$c->setIsCommenting($this->isAllowedToComment($this->getEntity()));
	return $c;
    }

    // </editor-fold>
    
//    private function isAllowedToComment(ICommentable $e) {
//	// maybe this should be within authorizator or commentControl with given user instance
//	$mode = $e->getCommentMode();
//	switch ($mode) {
//	    case CommentMode::ALLOWED:
//		return true;
//		break;
//	    case CommentMode::RESTRICTED:
//		return false;
//		break;
//	    case CommentMode::SIGNED:
//		return $this->getUser()->isLoggedIn();
//		break;
//	    case CommentMode::GROUP:
//		$ug = $this->getUser()->getIdentity()->getGroups();
//		$eg = $e->getGroups();
//		return true;
//		foreach ($ug as $g) {		    
//		    // ptam se, jestli ta skupina je v eg
//		}
//		break;
//	}
//    }
}
