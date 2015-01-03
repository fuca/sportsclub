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

namespace App\SystemModule\Model\Service;

use \App\Model\Entities\User,
    \App\Model\Entities\SportGroup;

/**
 * Interface for Sport group service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface ISportGroupService {

    /**
     * 
     * @param SportGroup $g
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createSportGroup(SportGroup $g);

    /**
     * 
     * @param SportGroup $g
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updateSportGroup(SportGroup $g);

    /**
     * 
     * @param numeric $id
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteSportGroup($id);

    /**
     * 
     * @param numeric $id
     * @param boolean $useCache
     * @return SportGroup
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getSportGroup($id, $useCache = true);

    /**
     * Returns SportGroup associated with given abbreviation
     * @param string $abbr
     * @return SportGroup
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\NoResultException
     * @throws Exceptions\DataErrorException
     */
    function getSportGroupAbbr($abbr);

    /**
     * Creates datasource for grid
     * @return \Grido\DataSources\Doctrine
     */
    function getSportGroupsDatasource();

    /**
     * Returns associative array of SportGroups and their ids
     * @param numeric $id
     * @param boolean|null $active
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getSelectAllSportGroups($id = null);

    /**
     * Returns associative array of SportGroups and their ids. Applicable means leaf node in groups tree.
     * @param numeric $id
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getSelectApplicablegroups($id = null);

    /**
     * Returns array of all SportGroups
     * @param numeric $root
     * @param boolean|null $active
     * @return array
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getAllSportGroups($root = null, $active = null);
}
