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
    \App\Model\Misc\Exceptions,
    \Nette\Utils\DateTime,
    \Nette\Caching\Cache,
    \Nette\Utils\Strings,
    \Kdyby\Doctrine\DBALException,
    \Grido\DataSources\Doctrine,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \App\SystemModule\Model\Service\ISportGroupService,
    \App\UsersModule\Model\Service\IUserService,
    \Doctrine\Common\Collections\ArrayCollection,
    \App\Model\Entities\Article,
    \App\Model\Misc\Enum\ArticleStatus,
    \Kdyby\Monolog\Logger,
    \App\SystemModule\Model\Service\ICommentable,  
    \App\Model\Entities\Comment,
    \Tomaj\Image\ImageService;
    //\Brabijan\Images\ImageStorage;

/**
 * Service for dealing with Article entities
 *
 * @author <michal.fuca.fucik(at)g.com>
 */
class ArticleService extends BaseService implements IArticleService {
    
    // image storage extension
    use \Brabijan\Images\TImagePipe;
    
    const   DEFAULT_IMAGE_PATH = "defaultImagePath",
	    DEFAULT_THUMBNAIL = "defafaultThumbnail",
	    DEFAULT_IMAGE = "defaultImage";
    
    const COLLECTION_HIGHLIGHTS  = "highlight";
    
    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $articleDao;
    
    /**
     * @var \App\SystemModule\Model\Service\ISportGroupService
     */
    private $sportGroupService;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    /**
     * @var array config parameters
     */
    private $config;
    
//    /**
//     * @var \Brabijan\Images\ImageStorage
//     */
//    private $imageStorage;
    
//    function setImageStorage(\Brabijan\Images\ImageStorage $imageStorage) {
//	$this->imageStorage = $imageStorage;
//    }
    
    /**
     * 
     * @var \Tomaj\Image\ImageService 
     */
    private $imageService;
    
    function setImageService(\Tomaj\Image\ImageService $imageService) {
	$this->imageService = $imageService;
    }
    
    public function getConfig() {
	return $this->config;
    }

    public function setConfig(array $config) {
	$this->config = $config;
    }
    
    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }
    
    public function getUserService() {
	return $this->userService;
    }
    
    public function setSportGroupService(ISportGroupService $sportGroupService) {
	$this->sportGroupService = $sportGroupService;
    }
    
    function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, Article::getClassName(), $logger);
	$this->articleDao = $em->getDao(Article::getClassName());
    }

    public function createArticle(Article $a) {
	if ($a === NULL)
	    throw new Exceptions\NullPointerException("Argument Article was null");
	try {
	    $this->entityManager->beginTransaction();
	    
	    $a->setAuthor($a->getEditor());
	    $a->setUpdated(new DateTime());
	    $this->sportGroupsTypeHandle($a);
	    $a->setAlias(Strings::webalize($a->getTitle()));
	    
	    $identifier = $this->imageService->storeNetteFile($a->getPicture());
	    $a->setPicture($identifier);
	    
	    $this->articleDao->save($a);
	    
	    $this->invalidateEntityCache();
	    $this->entityManager->commit();
	} catch (DBALException $ex) {
	    $this->entityManager->rollback();
	    $this->logWarning($ex);
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
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
	    return $a;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function editorTypeHandle(Article $a) {
	if ($a === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null");
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($a->getEditor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $a->setEditor($editor);
	    return $a;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    private function authorTypeHandle(Article $a) {
	if ($a === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null");
	try {
	    $author = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($a->getAuthor());
		if ($id !== null)
		    $author = $this->getUserService()->getUser($id, false);
	    }
	    $a->setAuthor($author);
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteArticle($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric, '$id' given");
	try {
	$db = $this->articleDao->find($id);
	    if ($db !== NULL) {
		$identifier = $db->getPicture();
		$this->imageService->removeResource($identifier);
		
		$this->articleDao->delete($db);
	    }
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getArticle($id, $useCache = true) {
	if ($id === NULL)
	    throw new Exceptions\NullPointerException("Argument Id was null");
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
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getArticleAlias($alias) {
	if (empty($alias)) 
	    throw new Exceptions\InvalidArgumentException("Argument alias was empty");
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load($alias);
	    if ($data === null) {
		$data = $this->articleDao->createQueryBuilder("a")
			->where("a.alias LIKE :alias")
			    ->setParameter("alias", $alias)
			->getQuery()->getSingleResult();
		$opts = [Cache::TAGS=>[self::ENTITY_COLLECTION, $alias, self::SELECT_COLLECTION]];
		$cache->save($alias, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getArticles(SportGroup $g = null, $limit = 20) {
	try {
	    if (is_null($g)) 
		return $this->articleDao->createQueryBuilder("a")
		    ->andWhere("a.status = :state")
			->setParameter("state", ArticleStatus::PUBLISHED)
		    ->getQuery()
		    ->getResult();
	    
	    $id = SportGroup::getClassName()."-".$g->getId();
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$qb = $this->articleDao->createQueryBuilder("a")
			->innerJoin('a.groups', 'g')
			->where('g.id = :gid')
			    ->setParameter("gid", $g->id)
			->andWhere("a.status = :state")
			    ->setParameter("state", ArticleStatus::PUBLISHED)
			->orderBy("a.updated", "DESC")
			->setMaxResults($limit);
		$data = $qb->getQuery()->getResult();
		$opts = [Cache::TAGS=>[self::ENTITY_COLLECTION, $id, self::SELECT_COLLECTION]];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Doctrine\ORM\NoResultException $ex) {
	    $this->logWarning($ex->getMessage());
	    throw new Exceptions\DataErrorException("No relations article -> group $g found");
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException("Error reading articles according to related group $g");
	}
    }

    public function updateArticle(Article $a) {
	if ($a === NULL)
	    throw new Exceptions\NullPointerException("Argument Article was null");
	try {
	    $this->entityManager->beginTransaction();
	    
	    $db = $this->articleDao->find($a->getId());
	    if ($db !== null) {
		$this->articlePictureHandle($a, $db);
		
		$db->fromArray($a->toArray());
		$this->sportGroupsTypeHandle($db);
		$db->setUpdated(new DateTime());
		$this->editorTypeHandle($db);
		$this->authorTypeHandle($db);
		$db->setAlias(Strings::webalize($a->getTitle()));
		
		$this->entityManager->merge($db);
		$this->entityManager->flush();
		$this->invalidateEntityCache($db);
		$this->entityManager->commit();
	    }
	} catch (DBALException $ex) {
	    $this->entityManager->rollback();
	    $this->logWarning($ex->getMessage());
	    throw new Exceptions\DuplicateEntryException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
	return $a;
    }
    
    private function articlePictureHandle(Article $incoming, Article $database) {
	if ($incoming->getPicture() == '') {
	    $incoming->setPicture($database->getPicture());
	} else {
	    $oldImageStorage = $database->getPicture();
	    $this->imageService->removeResource($oldImageStorage);
	    $identifier = $this->imageService->storeNetteFile($incoming->getPicture());
	    $incoming->setPicture($identifier);
	}
    }

    public function getArticlesDatasource() {
	$model = new Doctrine(
		$this->articleDao->createQueryBuilder('a'));
	return $model;
    }
    
    public function createComment(Comment $c, ICommentable $e) {
	try {
	    $this->entityManager->beginTransaction();
	    $wpDb = $this->articleDao->find($e->getId());
	    if ($wpDb !== null) {
		//$this->commentService->createComment($c);
		$ccs = $wpDb->getComments();
		//$ccs->clear(); // vymaze celou kolekci
		$ccs->add($c);
		$this->entityManager->merge($wpDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($wpDb);
	    }
	    $this->entityManager->commit();
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateComment(Comment $c, ICommentable $e) {
	try {
	    $this->entityManager->beginTransaction();
	    //$wpDb = $this->wallDao->find($e->getId());
	    $this->commentService->updateComment($c);
	    $this->invalidateEntityCache($e);
	    $this->entityManager->commit();
	} catch (\Exception $ex) {
	    $this->entityManager->rollback();
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteComment(Comment $c, ICommentable $e) {
	try {
	    $wpDb = $this->articleDao->find($e->getId());
	    if ($wpDb !== null) {
		$coll = $wpDb->getComments();
		$id = $c->getId();
		$comment = $coll->filter(function ($e) use ($id) {return $e->getId() == $id;})->first();
		$index = $coll->indexOf($comment);
		$coll->remove($index);

		$this->entityManager->merge($wpDb);
		$this->entityManager->flush($wpDb);
		$this->commentService->deleteComment($c->getId());    
		$this->invalidateEntityCache($wpDb);
	    }
	} catch (Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
    public function getHighLights($limit = 10) {
	try {
	    $cache = $this->getEntityCache();
	    $data = $cache->load(self::COLLECTION_HIGHLIGHTS);
	    if ($data === null) {
		$data = $this->articleDao->createQueryBuilder("a")
		    ->where("a.highlight = 1")
		    ->andWhere("a.status = :state")
			->setParameter("state", ArticleStatus::PUBLISHED)
		    ->orderBy("a.updated", "DESC")
		    ->setMaxResults($limit)
		    ->getQuery()->getResult();
		$opts = [Cache::TAGS=>[self::COLLECTION_HIGHLIGHTS, self::ENTITY_COLLECTION]];
		$cache->save(self::COLLECTION_HIGHLIGHTS, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    $this->logError($ex->getMessage());
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

}
