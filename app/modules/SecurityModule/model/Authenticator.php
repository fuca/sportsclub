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

namespace App\SecurityModule\Model;

use \Nette\Object,
    \Nette\DateTime,
    \Nette\Security\Identity,
    \Nette\Security\IAuthenticator,
    \Nette\Security\AuthenticationException,
    \Nette\Utils\Strings,
    \Kdyby\Monolog\Logger,
    \Nette\Security\Passwords,
    \App\Model\Misc\Exceptions,
    \App\UsersModule\Model\Service\IUserService,
    \App\Model\Service\IRoleService;
    

/**
 * ORM persistable entity representing acl rule
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class Authenticator extends Object implements IAuthenticator {

    /**
     * @var \App\Model\Service\IRoleService 
     */
    private $rolesService;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService 
     */
    private $usersService;

    /** @var Salt */
    private $salt;
    
    /**
     * @var \Kdyby\Monolog\Logger
     */
    private $logger;
    
    public function getLogger() {
	return $this->logger;
    }

    public function setLogger(Logger $logger) {
	$this->logger = $logger;
    }
    
    public function setSalt($salt) {
	$this->salt = $salt;
    }

    public function setRolesService(IRoleService $roleService) {
	$this->rolesService = $roleService;
    }

    public function setUsersService(IUserService $userService) {
	$this->usersService = $userService;
    }

    /**
     * @param Credentials  Prihlasovaci udaje.
     * @throws AuthenticationException Chyba v overeni udaju.
     * @return Identitu uzivatele.
     */
    public function authenticate(array $credentials) {
	list($username, $password) = $credentials;

	try {
	    $user = $this->usersService->getUserEmail($username);
	} catch (Exceptions\NoResultException $ex) {
	    $this->getLogger()->addAlert("### ATTEMPT TO LOG IN WITH INVALID EMAIL ### - exception = ".$ex);
	    throw new AuthenticationException("securityModule.loginControl.messages.invalidCredentials", self::IDENTITY_NOT_FOUND);
	}
	    
	if (!Passwords::verify($password, $user->password)) {
	    $this->getLogger()->addAlert("### ATTEMPT TO LOG IN WITH INVALID PASSWORD ### - tried password = ".$password);
	    throw new AuthenticationException("securityModule.loginControl.messages.invalidCredentials", self::INVALID_CREDENTIAL);
	}
	
	if (!$user->active) {
	    $this->getLogger()->addAlert("### ATTEMPT TO LOG TO INACTIVE ACCOUNT ### - account id = ".$user->getId());
	    throw new AuthenticationException("securityModule.loginControl.messages.userUnactive");
	}

	$this->usersService->updateLastLogin($user);
	$identity = $user;

	return $identity;
    }

}
