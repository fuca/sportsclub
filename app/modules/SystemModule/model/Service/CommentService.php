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

use 
    \App\Model\Misc\Exceptions,
    \App\Model\Service\BaseService,
    \Kdyby\Doctrine\EntityManager,
    \Nette\Utils\DateTime,
    \App\Model\Entities\Comment,
    \App\UsersModule\Model\Service\IUserService,
    \App\SystemModule\Model\Service\ICommentService;

/**
 * Service for managing comments
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
class CommentService extends BaseService implements ICommentService {
    
    /**
     * @var \Kdyby\Doctrine\EntityDao
     */
    private $commentDao;
    
    /**
     * @var \App\UsersModule\Model\Service\IUserService
     */
    private $userService;
    
    /** @var Event dispatched every time after create of Comment */
    public $onCreate = [];
    
    /** @var Event dispatched every time after update of Comment */
    public $onUpdate = [];
    
    /** @var Event dispatched every time after delete of Comment */
    public $onDelete = [];
    
    public function __construct(EntityManager $em) {
	parent::__construct($em, Comment::getClassName());
	$this->commentDao = $em->getDao(Comment::getClassName());
    }
    
    public function setUserService(IUserService $userService) {
	$this->userService = $userService;
    }

    public function getUserService() {
	return $this->userService;
    }

    public function createComment(Comment $c) {
	if ($c === null)
	    throw new Exceptions\NullPointerException("Argument comment was null");
	try {
	    $c->setUpdated(new DateTime());
	    $c->setCreated(new DateTime());
	    $c->setAuthor($c->getEditor());
	    $this->commentDao->save($c);
	    $this->invalidateEntityCache($c);
	    $this->onCreate($c);
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function updateComment(Comment $c) {
	if ($c === null)
	    throw new Exceptions\NullPointerException("Argument comment was null");
	try {
	    $cDb = $this->commentDao->find($c->getId());
	    if ($cDb !== null) {
		$cDb->fromArray($c->toArray());
		$cDb->setUpdated(new DateTime());
		$this->authorTypeHandle($cDb);
		$this->entityManager->merge($cDb);
		$this->entityManager->flush();
		$this->invalidateEntityCache($c);
		$this->onUpdate($c);
	    }
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getComment($id, $useCache = true) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    if (!$useCache) {
		return $this->commentDao->find($id);
	    }
	    $cache = $this->getEntityCache();
	    $data = $cache->load($id);
	    if ($data === null) {
		$data = $this->commentDao->find($id);
		$opts = [Cache::TAGS => [self::ENTITY_COLLECTION, $id]];
		$cache->save($id, $data, $opts);
	    }
	    return $data;
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function deleteComment($id) {
	if (!is_numeric($id))
	    throw new Exceptions\InvalidArgumentException("Argument id has to be type of numeric");
	try {
	    $cDb = $this->commentDao->find($id);
	    if ($cDb !== null) {
		$this->commentDao->delete($cDb);
		$this->invalidateEntityCache($cDb);
		$this->onDelete($cDb);
	    }
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }

    public function getComments() {
	try {
	    return $this->commentDao->findAll();    
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex->getMessage(), $ex->getCode(), $ex->getPrevious());
	}
    }
    
     private function editorTypeHandle(Comment $a) {
	if ($a === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null", 0);
	try {
	    $editor = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($a->getEditor());
		if ($id !== null)
		    $editor = $this->getUserService()->getUser($id, false);
	    }
	    $a->setEditor($editor);
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex);
	}
    }

    private function authorTypeHandle(Comment $a) {
	if ($a === null)
	    throw new Exceptions\NullPointerException("Argument Event cannot be null", 0);
	try {
	    $author = null;
	    if ($this->getUserService() !== null) {
		$id = $this->getMixId($a->getAuthor());
		if ($id !== null)
		    $author = $this->getUserService()->getUser($id, false);
	    }
	    $a->setAuthor($author);
	} catch (\Exception $ex) {
	    throw new Exceptions\DataErrorException($ex);
	}
    }    
}
