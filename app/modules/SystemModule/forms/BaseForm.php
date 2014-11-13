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

namespace App\Forms;

use \App\Model\Misc\Enum\FormMode,
    \Nette\Application\UI\Form,
    \Nette\ComponentModel\IContainer,
    \Nette\Localization\ITranslator;

/**
 * Description of BaseForm
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
abstract class BaseForm extends Form {

    /** @var form mode */
    private $mode;
    
    /** @var callable onSuccess handler */
    private $successHandler;
    
    private $showCancel;
    
    public function getShowCancel() {
	return $this->showCancel;
    }

    public function setShowCancel($showCancel = true) {
	$this->showCancel = $showCancel;
    }

    
    public function __construct(IContainer $parent = NULL, $name = NULL, ITranslator $translator) {
	parent::__construct($parent, $name);
	$this->mode = FormMode::CREATE_MODE;
	$this->setTranslator($translator);
	
	$renderer = $this->getRenderer();
	$renderer->wrappers['controls']['container'] = NULL;
	$renderer->wrappers['pair']['container'] = 'div class=form-group';
	$renderer->wrappers['pair']['.error'] = 'has-error';
	$renderer->wrappers['control']['container'] = 'div class=col-sm-9';
	$renderer->wrappers['label']['container'] = 'div class="col-sm-3 control-label"';
	$renderer->wrappers['control']['description'] = 'span class=help-block';
	$renderer->wrappers['control']['errorcontainer'] = 'span class=help-block';

	// make form and controls compatible with Twitter Bootstrap
	$this->getElementPrototype()->class('form-horizontal');

	foreach ($this->getControls() as $control) {
	    if ($control instanceof Controls\Button) {
		$control->getControlPrototype()->addClass(empty($usedPrimary) ? 'btn btn-primary' : 'btn btn-default');
		$usedPrimary = TRUE;
	    } elseif ($control instanceof Controls\TextBase || $control instanceof Controls\SelectBox || $control instanceof Controls\MultiSelectBox) {
		$control->getControlPrototype()->addClass('form-control');
	    } elseif ($control instanceof Controls\Checkbox || $control instanceof Controls\CheckboxList || $control instanceof Controls\RadioList) {
		$control->getSeparatorPrototype()->setName('div')->addClass($control->getControlPrototype()->type);
	    }
	}
    }

    public function setMode($mode) {
	if (array_key_exists($mode, FormMode::getOptions())) {
	    $this->mode = $mode;
	} else {
	    throw new \Nette\InvalidArgumentException("Passed mode must be one of FormMode's constants, '$mode' given");
	}
    }
    
    public function setSuccessHandler($successHandler) {
	if (!is_callable($successHandler))
	    throw new \App\Model\Misc\Exceptions\InvalidArgumentException("Passed argument has to be callable");
	$this->successHandler = $successHandler;
    }
    
    public function getSuccessHandler() {
	return $this->successHandler;
    }

    public function getMode() {
	return $this->mode;
    }
    
    public function isCreate() {
	return $this->getMode() == FormMode::CREATE_MODE;
    }
    
    public function isUpdate() {
	return $this->getMode() == FormMode::UPDATE_MODE;
    }

    /**
     * Method initializes inner components
     */
    protected function initialize() {
	if ($this->showCancel)
	    $this->addButton("cancel", "system.forms.cancelButton.label")
		->setAttribute("onclick", "history.go(-1);");
	//$this->addSubmit("submitButton", "system.forms.submitButton.label");
    }
}
