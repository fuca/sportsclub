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

namespace App\Model\Service;

use App\Model\Entities\User,
	\App\Model\Entities\Role;

/**
 * Interface for Role service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IRoleService {
    
    /**
     * 
     * @param \App\Model\Entities\Role $r
     * @throws NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws DataErrorException
     */
    function createRole(Role $r);
    
    /**
     * 
     * @param type $id
     * @return type
     * @throws NullPointerException
     * @throws InvalidArgumentException
     */
    function deleteRole($id);
    
    /**
     * 
     * @param \App\Model\Entities\Role $r
     * @throws NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws DataErrorException
     */
    function updateRole(Role $r);
    
    /**
     * Returns role within specified numeric identifier
     * @param numeric $id
     * @return \App\Model\Entities\Role
     * @throws \Nette\InvalidArgumentException
     */
    function getRole($id);
    
    /**
     * Returns role associated with user
     * @param \App\Model\Entities\User $u
     * @return type
     * @throws NullPointerException
     */
    function getUserRoles(User $u);
    
    /**
     * Returns role according to given name
     * @param string $name
     * @return Role
     */
    function getRoleName($name);
    
    /**
     * Returns all roles
     * @return array
     */
    function getRoles();
    
    /**
     * 
     * @return \Grido\DataSources\Doctrine
     */
    function getRolesDatasource();
    
    /**
     * Returns associative array of roles and their keys
     * @param numeric $id
     * @return array
     */
    function getSelectRoles($id = null);
    
}
