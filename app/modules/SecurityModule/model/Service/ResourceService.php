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

use \Kdyby\PresenterTree,
    \App\Model\Service\BaseService,
    \App\SecurityModule\Model\Resource,
    \Nette\Caching\Cache,
    \Kdyby\Monolog\Logger,
    \App\SecurityModule\Model\PresenterResource,
    \Nette\Reflection\Method,
    \App\SecurityModule\Model\MethodResource,
    \App\SecurityModule\Model\Service\IResourceService,
    \Kdyby\Doctrine\EntityManager,
    \Doctrine\Common\Annotations\Reader;

/**
 * ResourceService
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
class ResourceService extends BaseService implements IResourceService {
    
    const SECURED_AN_NAME = "\App\SecurityModule\Model\Misc\Annotations\Secured";

    /**
     * @var Kdyby\PresenterTree
     */
    private $presenterTree;
    
    /**
     * @var \Doctrine\Common\Annotations\Reader
     */
    public $annReader;

    /** @var array resources tree */
    private $tree;

    /** @var string class name */
    private $className;
    
    public function setAnnotationsReader(Reader $reader) {
	$this->annReader = $reader;
    }
    
    public function getAnnotationsReader() {
	return $this->annReader;
    }

    public function setPresenterTree(PresenterTree $pt) {
	$this->presenterTree = $pt;
    }

    public function __construct(EntityManager $em, Logger $logger) {
	parent::__construct($em, Resource::getClassName(), $logger);
    }

    /**
     * Returns array tree of all resources
     * @return array
     */
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
    
    private function buildTree() {
	$pt = $this->presenterTree;
	$data = [];
	foreach ($pt->presenters as $p) {
	    $refPre = $p->getPresenterReflection();
	    $secAnn = $this->getAnnotationsReader()
		    ->getClassAnnotation($refPre, self::SECURED_AN_NAME);
	    if ($secAnn) {
		$pRes = new PresenterResource($p->getClass(), $secAnn->getResource(), 
			null, [], $secAnn->getPrivileges());
		foreach ($p->getActions() as $key => $a) {
		    $actionName = substr(preg_replace("/:/", '\\', $key), 1);
		    
		    $refMeth = new Method($p->getClass(), "action" . ucfirst($a));
		    
		    $secAnnMeth = $this->getAnnotationsReader()->getMethodAnnotation($refMeth, self::SECURED_AN_NAME);
		    if ($secAnnMeth) {
			$aRes = new MethodResource($actionName, $secAnnMeth->getResource(), 
				$pRes->getId(), [], $secAnnMeth->getPrivileges());
			$pRes->addResource($aRes);
			
		    }
		}
		$data[$p->getClass()] = $pRes;
	    }
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
