<?php

/*
 * Copyright 2014 Michal Fučík <michal.fuca.fucik@gmail.com>
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

namespace App\SystemModule\Forms;

use \App\Forms\BaseForm,
    \Nette\Application\UI\Form;
	
/**
 * SearchForm for fulltext search
 *
 * @author Michal Fučík <michal.fuca.fucik@gmail.com>
 */
final class SearchForm extends BaseForm {
   
    public function initialize() {
	$this->addText("keyword", "systemModule.searchForm.keyword", 20)
		->getControlPrototype()->addAttributes(["placeholder"=>"systemModule.searchForm.keywordPlaceholder"]);
	$this->addSubmit("submit", "systemModule.searchForm.submit");
	$this->onSuccess[] = callback($this->presenter, "searchFormSuccess");
    }
}
