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
    
    function createPartner(Partner $p);
    function updatePartner(Partner $p);
    function getPartner($id, $useCache = true);
    function deletePartner($id);
    function getActivePartners();
    function getPartnersDatasource();
}
