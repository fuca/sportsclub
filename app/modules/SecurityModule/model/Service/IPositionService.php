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

namespace App\SecurityModule\Model\Service;

use \App\Model\Entities\User,
    \App\Model\Entities\Position,
    \App\Model\Entities\Role,
    \App\Model\Entities\SportGroup,
    \Kdyby\GeneratedProxy\__CG__\App\Model\Entities;

/**
 * Interface for Position service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IPositionService {

    /**
     * 
     * @param Position $p
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createPosition(Position $p);

    /**
     * 
     * @param Position $p
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     */
    function updatePosition(Position $p);

    /**
     * 
     * @param Position $p
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function deletePosition(Position $p);

    /**
     * 
     * @param User $user
     * @param Role $role
     * @return boolean
     * @throws Exceptions\DataErrorException
     */
    function deletePositionsWithRole(Entities\User $user, Role $role);

    /**
     * Returns array of positions which are associated with given user
     * @param User $user
     * @param boolean $useCache
     * @return array
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function getUserPositions(User $u);

    /**
     * @param SportGroup $g
     * @param boolean $useCache
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getPositionsWithinGroup(SportGroup $g, $useCache = null);

    /**
     * @param numeric $id
     * @return Position
     * @throws InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getPosition($id);

    /**
     * @param User $u
     * @param SportGroup $g
     * @param Role $r
     * @return Position
     * @throws Exceptions\NoResultException
     * @throws Exceptions\DataErrorException
     */
    function getUniquePosition(User $u, SportGroup $g, Role $r);

    /**
     * Creates Position's datasource
     * @return Doctrine
     */
    function getPositionsDatasource();
}
