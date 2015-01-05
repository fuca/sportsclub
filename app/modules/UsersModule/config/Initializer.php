<?php

namespace App\UsersModule\Config;

use \App\UsersModule\Model\Service\IUserService,
    \App\Model\Misc\Exceptions,
    \Kdyby\Monolog\Logger,
    \App\Model\Entities\User,
    \App\Model\Entities\Contact,
    \App\Model\Entities\Address;

/**
 * Users module Initializer,
 * perform initial operations designated to set application to working state
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class Initializer {
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;
    
    /**
     * Assoc array with values for default user
     * @var array $userValues
     */
    private $userValues;
    
    public function __construct(IUserService $userService, Logger $logger) {
	$this->userService = $userService;
	$this->logger = $logger;
	//$this->userInit();
    }
   
    public function getUserValues() {
	if (!isset($this->userValues))
	    throw new Exceptions\InvalidStateException("Property userValues is not correctly set");
	return $this->userValues;
    }

    public function setUserValues(array $userValues) {
	$this->userValues = $userValues;
    }
    
    public function userInit() {
	$user = null;
	$email = $this->userValues["contact"]["email"];
	try {
	    $user = $this->userService->getUserEmail($email);
	} catch (Exceptions\NoResultException $ex) {
	   $this->logger->addDebug($ex); 
	}
	
	if ($user === null) {
	    $this->logger->addInfo("Users module initializer - User - no user with email $email found. New one is gonna be created.");
	    
	    $addrValues = $this->userValues["contact"]["address"];
	    $address = new Address((array) $addrValues);
	    $address->applyAccountNumber($this->userValues["contact"]["address"]["accountNumber"]);
	    $address->applyIdentificationNumber($this->userValues["contact"]["address"]["in"]);
	    $address->applyTaxIdentificationNumber($this->userValues["contact"]["address"]["tin"]);
	    
	    $contValues = $this->userValues["contact"];
	    $contact = new Contact((array) $contValues);
	    $contact->setAddress($address);
	    
	    $userValues = $this->userValues;
	    unset($userValues["contact"]);
	    $user = new User((array) $userValues);
	    $user->setActive(true);
	    $user->setContact($contact);
	    $user->setBirthNumber("0000000000");
	    $this->userService->createUser($user);
	}
    }
}
