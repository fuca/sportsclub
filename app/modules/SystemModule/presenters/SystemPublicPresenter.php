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

use \App\SystemModule\Presenters\SecuredPresenter,
    \App\Model\Misc\Enum\LayoutSectionStyle,
    \App\SystemModule\Components\ContactControl;

/**
 * SecuredPresenter (Base presenter for secured section)
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
abstract class SystemPublicPresenter extends BasePresenter {
    
    /**
     * @inject
     * @var \App\UsersModule\Model\Service\IUserService
     */
    public $userService;
    
    private $defaultSystemUserId = 1;
    
    /**
     * Setter for configuration purposes
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     */
    public function setDefaultUserId($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	$this->defaultSystemUserId = $id;
    }
    
    public function getDefaultUserId() {
	if (!isset($this->defaultSystemUserId))
	    throw new Exceptions\InvalidStateException("Property defaultSystemUserId is not se, use appropriate setter first");
	return $this->defaultSystemUserId;
    }

    protected function beforeRender() {
	parent::beforeRender();
	$this->template->layoutStyle = LayoutSectionStyle::INFO;
    }
    
    public function createComponentContactControl($name) {
	$c = new ContactControl($this, $name);
	$user = null;
	$id = null;
	try {
	    $id = $this->getDefaultUserId();
	    $user = $this->userService->getUser($id);
	} catch (Exceptions\DataErrorException $ex) {
	    $this->handleDataLoad($id, "this", $ex);
	}
	$c->setUser($user);
	return $c;
    }

}
