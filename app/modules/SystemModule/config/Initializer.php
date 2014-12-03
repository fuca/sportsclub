<?php

namespace App\SystemModule\Config;

use \App\SystemModule\Model\Service\ISportGroupService,
    \App\Model\Misc\Exceptions,
    \Kdyby\Monolog\Logger,
    \App\Model\Entities\SportGroup;

/**
 * System module Initializer,
 * perform initial operations designated to set application to working state
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class Initializer {
    
    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $groupService;
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;
    
    /**
     * Assoc array with values for default root group
     * @var array $groupValues
     */
    private $groupValues;
    
    public function __construct(ISportGroupService $groupService, Logger $logger) {
	$this->groupService = $groupService;
	$this->logger = $logger;
	//$this->groupInit();
    }
   
    public function getGroupValues() {
	if (!isset($this->groupValues))
	    throw new Exceptions\InvalidStateException("Property groupValues is not correctly set");
	return $this->groupValues;
    }

    public function setGroupValues(array $groupValues) {
	$this->groupValues = $groupValues;
    }
    
    public function groupInit() {
	$group = null;
	$abbr = $this->groupValues["abbr"];
	try {
	    $group = $this->groupService
		    ->getSportGroupAbbr($abbr);
	} catch (Exceptions\NoResultException $ex) {
	   $this->logger->addDebug($ex); 
	}
	
	if ($group === null) {
	    $this->logger->addInfo("System module initializer - Sport Group - no group with abbr $abbr found. New one is gonna be created.");
	    $g = new SportGroup((array) $this->getGroupValues());
	    $this->groupService->createSportGroup($g);
	}
    }
}
