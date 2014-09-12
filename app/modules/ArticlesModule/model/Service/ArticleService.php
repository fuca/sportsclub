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

use App\Service\Exceptions\NullPointerException,
    App\Model\Entities\SportGroup,
    \Doctrine\ORM\NoResultException;

/**
 * Service for dealing with Article entities
 *
 * @author <michal.fuca.fucik(at)g.com>
 */
class ArticleService extends \Nette\Object implements IArticleService {
    
    /**
     * @var \Kdyby\Doctrine\EntityManager
     */
    private $entityManager;

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $articleDao;
    
    function __construct(\Kdyby\Doctrine\EntityManager $em) {
	$this->entityManager = $em;
	$this->articleDao = $em->getDao(\App\Model\Entities\Article::getClassName());
    }

    public function createArticle(\App\Model\Entities\Article $a) {
	if ($a === NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Article was null", 0);
	$this->articleDao->save($a);
    }

    public function deleteArticle(\App\Model\Entities\Article $a) {
	if ($a === NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Article was null", 0);
	$db = $this->articleDao->find($a->id);
	if ($db !== NULL) {
	    $this->articleDao->delete($db);
	} else {
	    throw new \App\Services\Exceptions\DataErrorException("Entity does not exist", 2);
	}
    }

    public function getArticle($id) {
	if ($id === NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Id was null", 0);
	$result = $this->articleDao->find($id);
	return $result;
    }

    public function getArticles(\App\Model\Entities\SportGroup $g) {
	if ($g === NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Group was null", 0);
	
	$qb = $this->articleDao->findAll();
	return $qb;
    }

    public function updateArticle(\App\Model\Entities\Article $a) {
	if ($a === NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Article was null", 0);
	$this->articleDao->save($a);
    }

}
