<?php

namespace App\SystemModule\Model\Service\Menu;

interface IProtectedMenuControlFactory {
    
    function getItems();
    
    function addItem($item);
}
