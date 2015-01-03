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

namespace App\PartnersModule\Model\Service;

use \App\Model\Entities\Partner;

/**
 * Interface for PartnerService
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
interface IPartnerService {

    /**
     * Creates Partner entry within database.
     * @param Partner $p
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createPartner(Partner $p);

    /**
     * Updates Partner's entry within database.
     * @param Partner $p
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updatePartner(Partner $p);

    /**
     * Returns Partner entity with given id
     * @param type $id
     * @param type $useCache
     * @return Partner
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getPartner($id, $useCache = true);

    /**
     * Deletes entry of Partner with given id
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deletePartner($id);

    /**
     * Finds Partners with active flag set to true
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getActivePartners();

    /**
     * Returns datasource for Grido
     * @return Doctrine
     */
    function getPartnersDatasource();
}
