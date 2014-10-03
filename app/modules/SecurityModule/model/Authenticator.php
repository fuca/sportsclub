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

use Nette\Object,
    Nette\DateTime,
    Nette\Security\Identity,
    Nette\Security\IAuthenticator,
    Nette\Security\AuthenticationException,
    Nette\Utils\Strings,
    \App\Misc\Passwords,
    \Doctrine\ORM\NoResultException,
    App\Model\Service\IUserService,
    App\Model\Service\IRoleService;
    

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
     * @var \App\Model\Service\IUserService 
     */
    private $usersService;

    /** @var Salt */
    private $salt;
    
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
	} catch (NoResultException $ex) {		////// tohle doctrina urcite nevyhazuje.... otestovat a predelat
	    throw new AuthenticationException("Špatné jméno nebo heslo", self::IDENTITY_NOT_FOUND);
	}
	    
	if (!Passwords::verify($password, $user->password))
	    throw new AuthenticationException("Špatné jméno nebo heslo", self::INVALID_CREDENTIAL);
	
	if (!$user->active) {
	    $name = "(" . $user->id . ")";
	    throw new AuthenticationException("Uživatel se zadaným emailem není aktivním členem této organizace. Pokud máte pocit, že je tato informace chybná, prosím kontaktujte sekretariát.");
	}

	$user->lastLogin = new DateTime();
	$roles = $this->rolesService->getUserRoles($user);
	$user->setRoles($roles);
	$identity = $user;

	return $identity;
    }

}
