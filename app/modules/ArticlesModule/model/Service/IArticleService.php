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

namespace App\ArticlesModule\Model\Service;

use \App\Model\Entities\SportGroup,
    \App\Model\Entities\Article;

/**
 * Interface for Article service
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IArticleService {
    
    /**
     * Saves article entity into database
     */
    function createArticle(Article $a);
    
    /**
     * Updates existing article entry within database
     */
    function updateArticle(Article $a);
    
    /**
     * Deletes existing entry from database
     */
    function deleteArticle($id);

    /**
     * Returns article entry from database/cache
     * @param \App\Model\Entities\SportGroup
     * @return Article
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function getArticle($id);
    
    function getArticleAlias($alias);
    
    /**
     * Returns article associated with given sportGroup
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    function getArticles(SportGroup $g);
    
    /**
     * Returns articles datasource
     * @return \Grido\DataSources\Doctrine
     */
    function getArticlesDatasource();
    
    /**
     * Returns highlighted articles
     * @param integer $limit
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getHighLights();
}
