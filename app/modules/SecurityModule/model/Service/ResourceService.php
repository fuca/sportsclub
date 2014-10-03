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

namespace App\SecurityModule\Model\Service;

use Kdyby\PresenterTree,
    \App\Model\Service\BaseService,
    \App\SecurityModule\Model\Resource,
    Nette\Caching\Cache,
    \App\SecurityModule\Model\PresenterResource,
    \Nette\Reflection\Method,
    \App\SecurityModule\Model\MethodResource,
    \App\SecurityModule\Model\Service\IResourceService,
    \Kdyby\Doctrine\EntityManager;

/**
 * ResourceService
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ResourceService extends BaseService implements IResourceService {

    /**
     * @var Kdyby\PresenterTree
     */
    private $presenterTree;

//    /**
//     * @var \App\Model\IModuleCacheService
//     */
//    private $cacheService;

    /** @var array resources tree */
    private $tree;

    /** @var string class name */
    private $className;

//    public function getClassName() {
//	if (!isset($this->className)) {
//	    $this->className = self::getReflection()->name;
//	}
//	return $this->className;
//    }

    public function setPresenterTree(PresenterTree $pt) {
	$this->presenterTree = $pt;
    }

//    public function setCacheService(App\Model\IModuleCacheService $cacheService) {
//	$this->cacheService = $cacheService;
//    }
    public function __construct(EntityManager $em) {
	parent::__construct($em, Resource::getClassName());
    }

    public function getResources() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::ENTITY_COLLECTION);
	if ($data == null) {
	    $data = $this->buildTree();
	    $opt = [Cache::TAGS => [self::ENTITY_COLLECTION]];
	    $cache->save(self::ENTITY_COLLECTION, $data, $opt);
	}
	return $data;
    }

    private function buildTree() {
	$pt = $this->presenterTree;
	$data = [];
	foreach ($pt->presenters as $p) {
	    $refPre = $p->getPresenterReflection();
	    if ($refPre->hasAnnotation(\SecuredAnnotation::ANNOTATION_NAME)) {
		$sA = $refPre->getAnnotation(\SecuredAnnotation::ANNOTATION_NAME);
		$pRes = new PresenterResource($p->getClass(), $sA->getResource(), 
			null, [], $sA->getPrivileges());
		foreach ($p->getActions() as $key => $a) {
		    $actionName = substr(preg_replace("/:/", '\\', $key), 1);
		    $refMeth = new Method($p->getClass(), "action" . ucfirst($a));
		    if ($refMeth->hasAnnotation(\SecuredAnnotation::ANNOTATION_NAME)) {
			$msa = $refMeth->getAnnotation(\SecuredAnnotation::ANNOTATION_NAME);
			$aRes = new MethodResource($actionName, $msa->getResource(), 
				$pRes->getId(), [], $msa->getPrivileges());
			$pRes->addResource($aRes);
		    }
		}
		$data[$p->getClass()] = $pRes;
	    }
	}
	return $data;
    }

//    public function getPrivileges($id) {
//	$res = $this->getResource($id);
//	return $ret = $res->getPrivileges() !== null? $ret: [];
//    }

    public function getResource($id) {
	$cache = $this->getEntityCache();
	$data = $cache->load($id);
	if ($data == null) {
	    $tree = $this->getResources();
	    $data = $this->deepSearch($id, $tree);
	    $opt = [Cache::TAGS => [self::ENTITY_COLLECTION, $id, self::SELECT_COLLECTION]];
	    $cache->save($id, $data, $opt);
	}
	return $data;
    }

    private function deepSearch($id, $tree) {
	$res = null;
	foreach ($tree as $r) {
	    if ($r->getId() == $id) {
		return $r;
	    } else {
		$res = $this->deepSearch($id, $r->getSubResources());
		if ($res != null) break;
	    }
	}
	return $res;
    }

    public function getSelectResources() {
	$cache = $this->getEntityCache();
	$data = $cache->load(self::SELECT_COLLECTION);
	if ($data == null) {
	    $tree = $this->getResources();
	    $data = $this->deepFlatten($tree);
	    $opt = [Cache::TAGS => [self::SELECT_COLLECTION]];
	    $cache->save(self::SELECT_COLLECTION, $data, $opt);
	}
	return $data;
    }

    private function deepFlatten($tree) {
	$selList = [];
	foreach ($tree as $r) {
	    $selList[$r->getId()] = $r->getLabel();
	    $subRs = $r->getSubResources();
	    if (!empty($subRs))
		$selList = $selList + $this->deepFlatten($subRs);
	}
	return $selList;
    }

}
