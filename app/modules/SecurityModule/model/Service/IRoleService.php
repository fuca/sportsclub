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
     * Reteurns role within specified numeric identifier
     * @param numeric $id
     * @return \App\Model\Entities\Role
     * @throws \Nette\InvalidArgumentException
     */
    function getRole($id);
    
    /**
     * 
     * @param \App\Model\Entities\User $user
     * @return type
     * @throws NullPointerException
     */
    function getUserRoles(User $u);
    
    function getRoleName($name);
    
    /**
     * 
     * @return type
     */
    function getRoles();
    
    /**
     * 
     * @return \Grido\DataSources\Doctrine
     */
    function getRolesDatasource();
    
    /**
     * 
     * @param type $id
     * @return type
     */
    function getSelectRoles($id = null);
    
}
