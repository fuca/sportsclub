<?php

namespace App\SecurityModule\Model\Misc\Annotations;

/**
 * SecuredAnnotation for marking methods needs to be authorizated
 * @Annotation
 * @author Michal Fučík
 */
class Secured {

    public $resource;
    public $privileges;

    public function getPrivileges() {
	return $this->privileges;
    }

    public function getResource() {
	return $this->resource;
    }

    public function __toString() {
	return "{$this->getResource()}";
    }
}
