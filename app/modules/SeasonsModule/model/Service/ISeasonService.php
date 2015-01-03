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

use App\Model\Entities\Season;

/**
 * Interface for Seasons service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface ISeasonService {

    /**
     * @param Season $s
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createSeason(Season $s);

    /**
     * @param Season $s
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updateSeason(Season $s);

    /**
     * @param numeric $id
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteSeason($id);

    /**
     * @param numeric $id
     * @param boolean $useCache
     * @return Season
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getSeason($id, $useCache = true);

    /**
     * Creates datasource for grid access
     * @return Doctrine
     */
    function getSeasonsDataSource();

    /**
     * Returns associative array of Season titles and their ids
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getSelectSeasons();

    /**
     * @param numeric $id
     * @throws Exceptions\DataErrorException
     */
    function setSeasonCurrent($id);

    /**
     * @return Season
     * @throws Exceptions\DataErrorException
     */
    function getCurrentSeason();
}
