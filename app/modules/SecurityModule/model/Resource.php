<?php

namespace App\SecurityModule\Model;

use \Nette\Object,
    \App\Model\Misc\Exceptions;

/**
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
abstract class Resource extends Object {

    /** @var string Absolute application resource class name */
    private $id;

    /** @var string label or rb key */
    private $label;
    
    /** @var string parent id */
    private $parent;

    /** @var array */
    private $privileges;

    /** @var array Array of child resources */
    private $subResources;

    public function __construct($id = null, $label = null, $parent = null, $sub = [], $privs = []) {
	$this->id = $id;
	$this->label = $label;
	$this->parent = $parent;
	$this->subResources = $sub;
	$this->privileges = $privs;
    }

    public function getId() {
	return $this->id;
    }

    public function getLabel() {
	return $this->label;
    }

    public function getSubResources() {
	return $this->subResources;
    }

    public function setId($id) {
	$this->id = $id;
    }

    public function setLabel($label) {
	$this->label = $label;
    }
    
    public function getParent() {
	return $this->parent;
    }

    public function setParent($parent) {
	$this->parent = $parent;
    }
    
    public function hasParent() {
	return !empty($this->parent);
    }

    public function addResource(Resource &$r) {
	$id = $r->getId();
	if (isset($this->subResources[$id]))
	    throw new Exceptions\InvalidStateException("Resource with id '{$id}' already exist");
	$this->subResources[$id] = $r;
    }

    public function getPrivileges() {
	return $this->privileges;
    }

    public function addPrivilege($p) {
	$this->privileges[] = $p;
    }

    public static function getClassName() {
	return get_called_class();
    }

}
