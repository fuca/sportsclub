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
     * @param User $u
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createUser(User $u);

    /**
     * Updates User's entity state within database
     * @param User $u
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function updateUser(User $u);

    /**
     * Deletes User entity from database
     * @param type $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DependencyException
     * @throws Exceptions\DataErrorException
     * @throws EntityNotFoundException
     */
    function deleteUser($id);

    /**
     * Returns User entity by specified numeric ID
     * @param type $id
     * @return type
     * @throws Exceptions\NullPointerException
     * @throws \Nette\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getUser($id);

    /**
     * Returns User entity by specified email address
     * @param type $email
     * @return type
     * @throws Exceptions\NoResultException
     */
    function getUserEmail($email);

    /**
     * Returns collection of all users within database
     * @return type
     * @throws Exceptions\DataErrorException
     */
    function getUsers();

    /**
     * Null active means all users, bool active means users with the same active value
     * @param inteter $id
     * @param bool|null $active
     * @return array of pairs
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getSelectUsers();

    /**
     * Returns datasource for grido datagrid
     * @return \Grido\DataSources\Doctrine
     */
    function getUsersDatasource();

    /**
     * Creates datasource for grid
     * @return Doctrine
     */
    function getWebProfilesToPermitDatasource();

    /**
     * Regenerate new password, stores in database and fires event
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function regeneratePassword($id);

    /**
     * Toggles user activity flag
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function toggleUser($id);

    /**
     * Permit web profile change
     * @param numeric $id
     * @param User $user
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function permitWebProfile($id, User $user);

    /**
     * Deny web profile change
     * @param numeric $id
     * @param User $user
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function denyWebProfile($id, User $user);

    /**
     * Hashes given word or generate random password
     * @param string $word
     * @return string
     */
    function generateNewPassword($word = null);

    /**
     * Updates last login information
     * @param User $u
     * @return User
     * @throws Exceptions\DataErrorException
     */
    function updateLastLogin(User $u);
}
