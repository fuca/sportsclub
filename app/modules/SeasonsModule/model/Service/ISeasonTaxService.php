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

use \App\Model\Entities\SeasonTax,
    \App\Model\Entities\SportGroup,
    \App\Model\Entities\Season;

/**
 * Interface for Season tax service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface ISeasonTaxService {

    /**
     * @param SeasonTax $t
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createSeasonTax(SeasonTax $t);

    /**
     * @param SeasonTax $t
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updateSeasonTax(SeasonTax $t);

    /**
     * @param numeric $id
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteSeasonTax($id);

    /**
     * @param numeric $id
     * @return SeasonTax
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getSeasonTax($id);

    /**
     * Returns SeasonTax for specific SportGroup
     * @param Season $s
     * @param SportGroup $sg
     * @return SeasonTax
     * @throws Exceptions\NoResultException
     * @throws Exceptions\DataErrorException
     */
    function getSeasonTaxSG(Season $s, SportGroup $sg);

    /**
     * Creates datasource for grid usage
     * @return Doctrine
     */
    function getSeasonTaxesDataSource();
}
