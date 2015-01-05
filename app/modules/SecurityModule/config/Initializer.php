<?php

namespace App\SecurityModule\Config;

use \App\SystemModule\Model\Service\ISportGroupService,
    \App\SecurityModule\Model\Service\IPositionService,
    \App\Model\Service\IAclRuleService,
    \App\Model\Service\IRoleService,
    \App\UsersModule\Model\Service\IUserService,
    \App\Model\Misc\Exceptions,
    \Kdyby\Monolog\Logger,
    \App\Model\Entities\Position,
    \App\Model\Entities\Role,
    \App\Model\Entities\AclRule;
   

/**
 * Security module Initializer,
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
     * @var \App\SystemModule\Model\Service\IUserService
     */
    private $userService;
    
    /**
     * @var \App\SystemModule\Model\Service\IPositionService
     */
    private $positionService;
    
    /**
     * @var \App\SystemModule\Model\Service\IRoleService
     */
    private $roleService;
    
    /**
     * @var \App\Model\Service\IAclRuleService
     */
    private $ruleService;
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;
    
    /**
     * Array with roleS definition values
     * @var array $rolesValues
     */
    private $rolesValues;
    
    
    /**
     * Email of default system administrator
     * For create of default position
     * @var string $defaultUserEmail
     */
    private $defaultUserEmail;
    
    public function __construct(ISportGroupService $groupService, 
	    IPositionService $positionService, IUserService $userService, 
	    IRoleService $roleService, IAclRuleService $ruleService, Logger $logger) {
	
	$this->groupService	= $groupService;
	$this->positionService	= $positionService;
	$this->userService	= $userService;
	$this->roleService	= $roleService;
	$this->ruleService	= $ruleService;
	$this->logger		= $logger;
    }
   
    public function getRolesValues() {
	if (!isset($this->rolesValues))
	    throw new Exceptions\InvalidStateException("Property rolesValues is not correctly set");
	return $this->rolesValues;
    }

    public function setRolesValues(array $rolesValues) {
	$this->rolesValues = $rolesValues;
    }
    
    public function getDefaultUserEmail() {
	if (!isset($this->defaultUserEmail))
	    throw new Exceptions\InvalidStateException("Property defaultUserEmail is not correctly set");
	return $this->defaultUserEmail;
    }

    public function setDefaultUserEmail($defaultUserEmail) {
	$this->defaultUserEmail = $defaultUserEmail;
    }

    public function rolesInit() {
	
	foreach ($this->rolesValues as $val) {
	    $role = null;
	    try {
		$role = $this->roleService
			->getRoleName($val);
	    } catch (Exceptions\NoResultException $ex) {
	       $this->logger->addDebug($ex); 
	    }
	
	    if ($role === null) {
		$this->logger->addInfo("Security module initializer - Role - no role with name $val found. New one is gonna be created.");
		$r = ["name"=>$val, "note"=>"System created"];
		$newOne = new Role((array) $r);
		$this->roleService->createRole($newOne);
	    }
	}
    }
    
    public function positionsInit() {
	$user = $this->userService->getUserEmail($this->getDefaultUserEmail());
	$role = $this->roleService->getRoleName("admin");
	$group = $this->groupService->getSportGroupAbbr("root");
	$pos = null;
	try {
	    $pos = $this->positionService->getUniquePosition($user, $group, $role);
	} catch (Exceptions\NoResultException $ex) {
	    $this->logger->addDebug($ex->getMessage()); 
	}
	if ($pos === null) {
	    $this->logger->addInfo("Security module initializer - Position - no position with user $user, role $role and group $group found. New one is gonna be created.");
	    $pos = new Position();
	    $pos->setGroup($group);
	    $pos->setOwner($user);
	    $pos->setRole($role);
	    $pos->setName("Webmaster");
	    $pos->setComment("System created");
	    $this->positionService->createPosition($pos);
	}
	
    }
    
    public function rulesInit() {
	$role = $this->roleService->getRoleName("admin");
	$rule = null;
	try {
	    $rule = $this->ruleService->getUniqueRule($role);	    
	} catch (Exceptions\NoResultException $ex) {
	    $this->logger->addDebug($ex->getMessage());
	}
	
	if ($rule === null) {
	    $this->logger->addInfo("Security module initializer - AclRules - no godlike Rule for role $role found. New one is gonna be created.");
	    $rule = new AclRule();
	    $rule->setRole($role);
	    $rule->setResource(null);
	    $rule->setPrivilege(null);
	    $rule->setMode(\App\Model\Misc\Enum\AclMode::PERMIT);
	    $this->ruleService->createRule($rule);
	}
	
    }
    
}
