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
     * @staticvar string Flash messages type
     */
    const FM_SUCCESS = "success",
	    FM_ERROR = "error",
	    FM_WARNING = "warning";

    /** @var string @persistent */
    public $ajax = 'on';

    /** @var actual managing entity entity id */
    private $entityId;

    public function getEntityId() {
	return $this->entityId;
    }

    /** @var string @persistent */
    public $filterRenderType = Filter::RENDER_INNER;

    public function startup() {
	parent::startup();
	$this->entityId = $this->getParameter(self::NUM_IDENTIFIER);
    }

    protected function beforeRender() {
	parent::beforeRender();
	$this->setLayout('publicLayout');
	//$this->template->layoutsPath = '../../../../templates/';
	$this->template->layoutsPath = APP_DIR . "/modules/SystemModule/templates/";
	$this->template->actions = [];//$this->getResources();
	//$this->getContext()->getService('presenterTree');
    }

    public function getSalt() {
	$configParams = $this->presenter->context->getParameters();
	return $configParams['models']['salt'];
    }
}
