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

use \App\SystemModule\Presenters\BasePresenter,
    \Nette\Security\User;

/**
 * SecuredPresenter
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
abstract class SecuredPresenter extends BasePresenter {
    
    public function startup() {
	parent::startup();
	$user = $this->getUser();
	if (!$user->isLoggedIn()) {
	    if ($user->getLogoutReason() === User::INACTIVITY) {
		$this->flashMessage($this->tt("securityModule.loginControl.messages.outCosInactive"), self::FM_WARNING);
	    }

	    $backlink = $this->storeRequest();
	    $this->redirect(':Security:Auth:in', ['backlink' => $backlink]);
	}
    }

}