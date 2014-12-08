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

namespace App\UsersModule\Model\Service;

use \App\Model\Entities\User;

/**
 * Interface for User service
 *
 * @author <michal.fuca.fucik(at)gmail.com>
 */
interface IUserService {
    
    /**
     * Creates new User entity
     */
    function createUser(User $u);
    
    /**
     * Updates User's entity state within database
     */
    function updateUser(User $u);
    
    /**
     * Deletes User entity from database
     */
    function deleteUser($id);
    
    /**
     * Returns User entity by specified numeric ID
     * @return \App\Model\Entities\User
     */
    function getUser($id);
    
    /**
     * Returns User entity by specified email address
     */
    function getUserEmail($email);

    /**
     * Returns collection of all users within database
     */
    function getUsers();
    
    function getSelectUsers();
    
    function getUsersDatasource();
    
    function getWebProfilesToPermitDatasource();
    
    function regeneratePassword($id);
    
    function toggleUser($id);
    
    function permitWebProfile($id, User $user);
    
    function denyWebProfile($id, User $user);
    
    function generateNewPassword($word = null);
    
    function updateLastLogin(User $u);
    
    /**
     * Returns collection of Users according to given SportGroup
     */
  //function getUsers(SportGroup $g);
}

