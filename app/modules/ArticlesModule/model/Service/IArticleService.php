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

use App\Model\Entities\Article,
    App\Model\Entities\SportGroup;

/**
 * Interface for Article service
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IArticleService {
    
    function createArticle(Article $a);
    
    function updateArticle(Article $a);
    
    function deleteArticle($id);

    function getArticle($id);
    
    function getArticles(SportGroup $g);
    
    function getArticlesDatasource();
    
}
