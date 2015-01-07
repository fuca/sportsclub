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
    \App\Model\Misc\Enum\LayoutSectionStyle;

/**
 * SecuredPresenter (Base presenter for secured section)
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
abstract class SystemUserPresenter extends SecuredPresenter {
    
    public function checkRequirements($element) {
	parent::checkRequirements($element);
	
	if ($element instanceof \Nette\Application\UI\PresenterComponentReflection) {
	    $secAnn = $this->annotationReader->getClassAnnotation($element, "\App\SecurityModule\Model\Misc\Annotations\Secured");
	    if ($secAnn) {
		if (!$this->getUser()->isAllowed($element->getName())) {
		    $this->flashMessage($this->tt("securityModule.authorization.noPrivilegesSection"), self::FM_ERROR);
		    $this->redirect(':System:Default:userRoot');
		}
	    }
	}

	if ($element instanceof \Nette\Reflection\Method) {
	    $secAnn = $this->annotationReader->getMethodAnnotation($element, "\App\SecurityModule\Model\Misc\Annotations\Secured");
	    if ($secAnn) {
		if (!$this->getUser()->isAllowed($element->getDeclaringClass()->name, $element->getName())) {
		    $this->flashMessage($this->tt("securityModule.authorization.noPrivilegesAction"), self::FM_ERROR);
		    $this->redirect('default');
		}
	    }
	}
    }  
    
    public function beforeRender() {
	parent::beforeRender();
	$this->template->layoutStyle = LayoutSectionStyle::USER;
    }
}
