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

use \Nette\Diagnostics\Debugger,
    App\Model\Entities\WallPost,
    \App\Model\Entities\SportGroup,
    App\Services\Exceptions\NullPointerException;

/**
 * Description of WallService
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class WallService extends \Nette\Object implements IWallService {

    /**
     * @var \Kdyby\Doctrine\EntityManager
     */
    private $entityManager;

    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $wallDao;

    public function __construct(\Kdyby\Doctrine\EntityManager $em) {
	$this->entityManager = $em;
	$this->wallDao = $em->getDao(\App\Model\Entities\WallPost::getClassName());
    }

    public function createWallPost(WallPost $w) {
	if ($w == null)
	    throw new NullPointerException("Argument WallPost cannot be null", 0);
	$this->wallDao->save($w);
    }

    public function getWallPost($id) {
	if (!is_numeric($id))
	    throw new \Nette\InvalidArgumentException("Argument id has to be type of numeric, '$id' given", 1);
	return $this->wallDao->find($id);
    }

    public function getWallPosts(SportGroup $g) {
	if ($g == null)
	    throw new NullPointerException("Argument SportGroup cannot be null", 0);
	$qb = $this->entityManager->createQueryBuilder();
	$qb->select('w')
		->from('App\Model\Entities\WallPost', 'w')
		->innerJoin('e.groups', 'g')
		->where('g.id = :gid')
		->setParameter("gid", $g->id);
	return $qb->getQuery()->getResult();
    }

    public function removeWallPost(WallPost $w) {
	if ($w == null)
	    throw new NullPointerException("Argument WallPost cannot be null", 0);
	$this->wallDao->delete($w);
    }

    public function updateWallPost(WallPost $w) {
	if ($w == null)
	    throw new NullPointerException("Argument WallPost cannot be null", 0);
	$this->wallDao->save($w);
    }

}
