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
    \App\SecurityModule\Model\Misc\Annotations\Secured;

/**
 * Presenter serving as root of all sections
 * @Secured(resource="SystemDefaultSectionRoot")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class DefaultPresenter extends SecuredPresenter {
    
    public function renderDefault() {
	$this->template->layoutStyle = LayoutSectionStyle::INFO;
	$this->template->adminMenuPredicate = $this->user->isAllowed("App\SystemModule\Presenters\DefaultPresenter","App\System\Presenters\Default\adminRoot");
	$this->template->clubMenuPredicate = $this->user->isAllowed("App\SystemModule\Presenters\DefaultPresenter","App\System\Presenters\Default\clubRoot");
    }
    
    /**
     * @Secured(resource="ClubRoot")
     */
    public function actionClubRoot() {
	$this->template->layoutStyle = LayoutSectionStyle::CLUB;
	// render club menu
    }
    
    public function renderUserRoot() {
	$this->template->layoutStyle = LayoutSectionStyle::USER;
	// render user menu
    }
    
    /**
     * @Secured(resource="AdminRoot")
     */
    public function actionAdminRoot() {
	$this->template->layoutStyle = LayoutSectionStyle::ADMIN;
	// render admin menu
    }
}