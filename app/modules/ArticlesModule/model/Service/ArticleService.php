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

use \App\Service\Exceptions\NullPointerException,
    \App\Model\Entities\SportGroup,
    \Doctrine\ORM\NoResultException,
    \App\Model\Misc\Exceptions,
    \App\Model\Entities\Article,
    \Nette\Utils\DateTime,
    \Nette\Caching\Cache,
    \Grido\DataSources\Doctrine,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\Model\Service\IUserService,
    \Doctrine\Common\Collections\ArrayCollection;

/**
 * Service for dealing with Article entities
 *
 * @author <michal.fuca.fucik(at)g.com>
 */
class ArticleService extends BaseService implements IArticleService {
    
    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $articleDao;
    
    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;
    
    /**
     * 
     * @var \App\Model\Service\IUserService
     */
    private $userService;
    
    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }
    
    public function getUserService() {
	return $this->userService;
    }
    
    public function setSportGroupService(ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }
    
    function __construct(EntityManager $em) {
	parent::__construct($em, Article::getClassName());
	$this->articleDao = $em->getDao(Article::getClassName());
    }

    public function createArticle(Article $a) {
	if ($a === NULL)
	    throw new Exceptions\NullPointerException("Argument Article was null", 0);
	try {
	    $a->setAuthor($a->getEditor());
	    $a->setUpdated(new DateTime());
	    $this->sportGroupsTypeHandle($a);
	    $a->setPictureName("defaultArticleImage.png");
	    $this->articleDao->save($a);
	    $this->invalidateEntityCache();
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function sportGroupsTypeHandle(Article $a) {
	if ($a === null)
	    throw new Exceptions\NullPointerException("Argument event was null");
	try {
	    $coll = new ArrayCollection();
	    foreach ($a->getGroups() as $ag) {
	    $dbG = $this->sportGroupService->getSportGroup($ag, false);
		if ($dbG !== null) {
		   $coll->add($dbG); 
		}
	    }
	    $a->setGroups($coll);
	} catch (\Exception $a) {
	    throw new Exceptions\DataErrorException($a->getMessage(), $a->getCode(), $a->getPrevious());
	}
	return $a;
    }
    
    private function editorTypeHandle(Article $a) {
	if ($a === null)
	    throw new NullPointerException("Argument Event cannot be null", 0);
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($a->getEditor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $a->setEditor($editor);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }
    
    private function authorTypeHandle(Article $a) {
	if ($a === null)
	    throw new NullPointerException("Argument Event cannot be null", 0);
	try {
	    $author = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($a->getAuthor());
		if ($id !== null)
		    $author = $this->getUserService()->getUser($id, false);
	    }
	    $a->setAuthor($author);
	} catch (Exception $ex) {
	    throw new DataErrorException($ex);
	}
    }

    public function deleteArticle($id) {
	if ($id === NULL)
	    throw new Exceptions\NullPointerException("Argument id was null", 0);
	if (!is_numerict($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	
	$db = $this->articleDao->find($id);
	if ($db !== NULL) {
	    $this->articleDao->delete($db);
	} else {
	    throw new Exceptions\DataErrorException("Entity does not exist", 2);
	}
    }

    public function getArticle($id, $useCache = true) {
	if ($id === NULL)
	    throw new Exceptions\NullPointerException("Argument Id was null", 0);
	try {
	    if (!$useCache) {
		return $this->articleDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->articleDao->find($id);
		$opts = [Cache::TAGS => [self::ENTITY_COLLECTION, self::SELECT_COLLECTION, $id]];
		$cache->save($id, $data, $opts);
	    }
	    return $data;    
	} catch (\Exception $ex) {
	    $this->logger->addError($ex->getMessage());
	    throw new Exceptions\DataErrorException("Error reading article wit id $id");
	}
    }

    public function getArticles(SportGroup $g) {
	if ($g === NULL)
	    throw new Exceptions\NullPointerException("Argument Group was null", 0);
	$qb = $this->entityManager->createQueryBuilder();
	try {
	$qb->select('a')
		->from('App\Model\Entities\Article', 'a')
		->innerJoin('a.groups', 'g')
		->where('g.id = :gid')
		->setParameter("gid", $g->id);
	return $qb->getQuery()->getResult();
	} catch (\Doctrine\ORM\NoResultException $ex) {
	   $this->logger->addWarning("No relations article -> group $g found");
	   throw new Exceptions\DataErrorException("No relations article -> group $g found");
	} catch (\Exception $ex) {
	    $this->logger->addError("Error reading articles related to group $g");
	    throw new Exceptions\DataErrorException("Error reading articles according to related group $g");
	}
    }

    public function updateArticle(Article $a) {
	if ($a === NULL)
	    throw new Exceptions\NullPointerException("Argument Article was null", 0);
	try {
	    $this->entityManager->beginTransaction();
	    $db = $this->articleDao->find($a->getId());
	    if ($db !== null) {
		$db->fromArray($a->toArray());
		$this->sportGroupsTypeHandle($db);
		$db->setUpdated(new DateTime());
		$this->editorTypeHandle($db);
		$this->authorTypeHandle($db);
		$this->entityManager->merge($db);
		$this->entityManager->flush();
		$this->invalidateEntityCache($db);
	    }
	     $this->entityManager->commit();
	} catch (\Exception $ex) {
	    $this->logger->addError("Error updating article $db");
	    throw new Exceptions\DataErrorException("Error updating article - \n".$ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $a;
    }

    public function getArticlesDatasource() {
	$model = new Doctrine(
		$this->articleDao->createQueryBuilder('a'));
	return $model;
    }

}
