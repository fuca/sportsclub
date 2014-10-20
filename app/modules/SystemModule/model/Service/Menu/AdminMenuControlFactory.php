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

namespace App\SystemModule\Model\Service\Menu;

use \App\SystemModule\Model\Service\Menu\IAdminMenuControlFactory;

/**
 * AdminMenuControlFactory
 * 
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class AdminMenuControlFactory implements IAdminMenuControlFactory {
    
    private $items;
    
    public function getItems() {
	return $this->items;
    }
    
    public function addItem($item) {
	
    }
        
    public function create() {
	
    }
}

