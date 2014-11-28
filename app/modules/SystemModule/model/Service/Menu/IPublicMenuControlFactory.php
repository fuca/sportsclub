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

/**
 * IPublicMenuControlFactory
 * 
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
interface IPublicMenuControlFactory {
    
    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    function getItems();
    
    /**
     * Adds item into collection
     * @param IItemData
     * @throws Exceptions\InvalidStateException
     */
    function addItem($item);
    
    /**
     * Returns desired component
     * @param \Nette\Application\UI\Presenter
     * @param string 
     * @return \App\Components\MenuControl
     */
    function createComponent($parent, $name);
    
    /**
     * Invalidates cache of PublicMenu
     * Suitable to call after update of any entity within public menu control.
     */
    function invalidateCache();
}
