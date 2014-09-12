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

use \App\Model\Entities\Event,
    \App\Model\Entities\SportGroup,
    \App\Model\Entities\User,
    \App\Model\Entities\Forum;

/**
 * Implementation of service dealing with Forum entities
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class ForumService extends \Nette\Object implements IForumService {

    /**
     * @var \Kdyby\Doctrine\EntityManager
     */
    private $entityManager;

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $forumDao;

    function __construct(\Kdyby\Doctrine\EntityManager $em) {
	$this->entityManager = $em;
	$this->forumDao = $em->getDao(Forum::getClassName());
    }

    public function createForum(Forum $f) {
	if ($f == NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Forum was null", 0);
	$this->forumDao->save($f);
    }

    public function deleteForum(Forum $f) {
	if ($f == NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Forum was null", 0);
	$db = $this->forumDao->find($f);
	if ($db !== NULL) {
	    $this->forumDao->delete($db);
	} else {
	    throw new \App\Services\Exceptions\DataErrorException("Entity not found", 2);
	}
    }

    public function getForum($id) {
	if ($id == NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Id was null", 0);
	$res = $this->forumDao->find($id);
	return $res;
    }

    public function getForums(SportGroup $g) {
	if ($g == NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument SportGroup was null", 0);
	$qb = $this->entityManager->createQueryBuilder();
	$qb->select('f')
		->from('App\Model\Entities\Forum', 'f')
		->innerJoin('f.groups', 'g')
		->where('g.id = :gid')
		->setParameter("gid", $g->id);
	return $qb->getQuery()->getResult();
    }

    public function updateForum(Forum $f) {
	if ($f == NULL)
	    throw new \App\Services\Exceptions\NullPointerException("Argument Forum was null", 0);
	$this->forumDao->save($f);
    }

}
