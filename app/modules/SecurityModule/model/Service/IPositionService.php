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
    
    function createPosition(Position $p);
    
    function updatePosition(Position $p);
    
    function deletePosition(Position $p);
    
    function deletePositionsWithRole(Entities\User $user, Role $role);
    
    function getUserPositions(User $u);
    
    function getPositionsWithinGroup(SportGroup $g, $useCache = null);
    
    function getPosition($id);
    
    function getPositionsDatasource();
    
}