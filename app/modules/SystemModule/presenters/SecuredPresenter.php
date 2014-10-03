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

use App\SystemModule\Presenters\BasePresenter;

/**
 * SecuredPresenter (Base presenter for secured section)
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
abstract class SecuredPresenter extends BasePresenter {
    
    public function checkRequirements($element) {
	parent::checkRequirements($element);
	$user = $this->getUser();
	if (!$user->isLoggedIn()) {
		if ($user->getLogoutReason() === \Nette\Security\User::INACTIVITY) {
                $this->flashMessage('Uplynula maximální doba neaktivity! Systém vás z bezpečnostních důvodů odhlásil.', 'warning');
            }

            $backlink = $this->storeRequest();
            $this->redirect(':System:Auth:in', array('backlink' => $backlink));
	    }
	    
	if ($element->hasAnnotation(self::SECURED_ANNOTATION_ID)) {
	    $secAnn = $element->getAnnotation(self::SECURED_ANNOTATION_ID);
	    
//	    if (!$user->isAllowed($element->getName(), $secAnn->getPrivileges())) {
//		$this->flashMessage('Na vstup do této sekce nemáte dostatečné oprávnění!', self::FM_WARNING);
//                $this->redirect('Homepage:default');
//	    }
	    // asi by se tu mely proverovat ty skupinovy a vlastnicky prava, ci co..
	}
    }
    
    protected function beforeRender() {
	parent::beforeRender();
	
    }
    
    public function actionDefault() {
    }
    
}
