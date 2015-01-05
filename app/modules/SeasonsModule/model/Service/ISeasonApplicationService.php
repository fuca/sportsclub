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

namespace App\SeasonsModule\Model\Service;

use \App\Model\Entities\SeasonApplication,
    \App\Model\Entities\User,
    \App\Model\Entities\Season;

/**
 * Interface for Event service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface ISeasonApplicationService {

    /**
     * @param SeasonApplication $app
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\NoResultException
     * @throws Exceptions\InvalidStateException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createSeasonApplication(SeasonApplication $app);

    /**
     * @param SeasonApplication $app
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updateSeasonApplication(SeasonApplication $app);

    /**
     * @param numeric $id
     * @throws Exceptions\NullPointerException
     * @throws Exeptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteSeasonApplication($id);

    /**
     * Created datasource for grid
     * @return Doctrine
     */
    function getSeasonApplicationsDataSource();

    /**
     * @param numeric $id
     * @param boolean $useCache
     * @return SeasonApplication
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getSeasonApplication($id);

    /**
     * Checks whether given applicated season is still applicable
     * @param SeasonApplication $app
     * @return boolean
     */
    function isApplicationTime(SeasonApplication $app);
    
    /**
     * Find unique application according to given User and Season
     * @param User $u
     * @param Season $s
     * @return SeasonApplication
     * @throws Exceptions\DataErrorException
     */
    public function getUsersApplication(User $u, Season $s);
}
