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

namespace App\MotivationModule\Model\Service;

use \App\Model\Entities\MotivationTax,
    \App\Model\Entities\Season,
    \App\Model\Entities\SportGroup;

/**
 * Interface for MotivationTaxService
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
interface IMotivationTaxService {

    /**
     * @param MotivationTax $t
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createTax(MotivationTax $t);

    /**
     * @param MotivationTax $t
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updateTax(MotivationTax $t);

    /**
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteTax($id);

    /**
     * @param numeric $id
     * @return MotivationTax
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getTax($id);

    /**
     * Creates datasource for grid
     * @return Doctrine
     */
    function getTaxesDatasource();
    
    /**
     * Return one intance based on given season and group
     * @param Season $s
     * @param SportGroup $sg
     * @return MotivationTax
     * @throws Exceptions\DataErrorException
     */
    function getTaxSeason(Season $s, SportGroup $sg);
}
