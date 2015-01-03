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

use \App\Model\Entities\AclRule,
    \App\Model\Entities\Role;

/**
 * Interface for AclRule service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IAclRuleService {

    /**
     * @param numeric $id
     * @param boolean $useCache
     * @return AclRule
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getRule($id);

    /**
     * Returns all rules
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getRules();

    /**
     * @param AclRule $arule
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createRule(AclRule $arule);

    /**
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteRule($id);

    /**
     * @param AclRule $arule
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updateRule(AclRule $arule);

    /**
     * Creates datasource for Rule datagrid
     * @return \Grido\DataSources\Doctrine
     */
    function getRulesDatasource();

    /**
     * 
     * @param Role $r
     * @param string $resource
     * @param string $priv
     * @return AclRule
     * @throws Exceptions\NoResultException
     */
    function getUniqueRule(Role $r, $resource = null, $priv = null);
}
