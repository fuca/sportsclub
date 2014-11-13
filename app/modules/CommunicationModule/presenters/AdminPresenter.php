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

namespace App\CommunicationModule\Presenters;

use \App\SystemModule\Presenters\SecuredPresenter,
    \App\Model\Misc\Enum\FormMode,
    \App\ForumModule\Forms\ForumForm,
    \App\Model\Misc\Enum\CommentMode,
    \Nette\Application\UI\Form,
    \Nette\Utils\ArrayHash,
    \App\Model\Entities\Forum,
    \Grido\Grid,
    \App\SecurityModule\Model\Misc\Annotations\Secured;

/**
 * AdminSectionCommunicationPresenter
 * @Secured(resource="ForumAdmin")
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class AdminPresenter extends SecuredPresenter {
    
 
}
