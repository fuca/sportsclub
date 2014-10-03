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

use Nette,
    Nette\InvalidArgumentException,
    Nette\Application\UI\Presenter,
    \Grido\Components\Filters\Filter;

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

    /** @var actual managing entity entity id */
    private $entityId;

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
	return $this->entityId;
    }
    
    public function getTranslator() {
	return $this->translator;
    }

    /** @var string @persistent */
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

    // <editor-fold desc="COMPONENT FACTORIES">

    public function createComponentLoginControl($name) {
	$c = new \App\SystemModule\Components\LogInControl($this, $name);
	$p = $this;

	$c->setLogInTarget(":System:Public:default");
	return $c;
    }

    // </editor-fold>
}
