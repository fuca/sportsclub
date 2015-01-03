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

use \App\Model\Entities\StaticPage,
    \App\Model\Entities\SportGroup;

/**
 * Interface for Static pages service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IStaticPageService {

    /**
     * @param StaticPage $sp
     * @throws Exceptions\DataErrorException
     */
    function createStaticPage(StaticPage $sp);

    /**
     * 
     * @param StaticPage $sp
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function updateStaticPage(StaticPage $sp);

    /**
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DependencyException
     * @throws Exceptions\DataErrorException
     */
    function deleteStaticPage($id);

    /**
     * @param numeric $id
     * @param boolean $useCache
     * @return StaticPage
     * @throws \Nette\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getStaticPage($id, $useCache = true);

    /**
     * Finds static page according to given abbreviation
     * @param string $abbr
     * @param boolean $useCache
     * @return StaticPage
     * @throws Exceptions\DataErrorException
     */
    function getStaticPageAbbr($abbr, $useCache = true);

    /**
     * Creates Grido Doctrine datasource
     * @return \Grido\DataSources\Doctrine
     */
    function getPagesDataSource();

    /**
     * Reurns array of static pages belongs to given SportGroup
     * @param SportGroup $g
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getGroupStaticPages(SportGroup $g);

    /**
     * Returns associative array of StaticPages and their ids
     * @param boolean $useCache
     * @return StaticPage
     * @throws Exceptions\DataErrorException
     */
    function getSelectStaticPages($useCache = true);
}
