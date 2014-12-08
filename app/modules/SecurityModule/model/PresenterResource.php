<?php

namespace App\SecurityModule\Model;
use \App\SecurityModule\Model\Resource;
/**
 * Description of PresenterResource
 *
 * @author fuca
 */
class PresenterResource extends Resource {
    
    public function getPrivileges() {
	// vratit select akci
	$subs = $this->getSubResources();
	$arrSel = [];
	foreach ($subs as $key=>$sub) {
	    $arrSel[$key] = $sub->getLabel();
	}
	return $arrSel;
    }
}
