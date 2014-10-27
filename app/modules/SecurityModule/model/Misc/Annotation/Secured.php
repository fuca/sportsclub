<?php

namespace App\SecurityModule\Model\Misc\Annotations;

/**
 * Description of SecuredAnnotation
 * @Annotation
 * @author fuca
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
	return self::PRIVILEGE_ID . "={$this->getPrivileges()}," . self::RESOURCE_ID . "={$this->getResource()}";
    }

}
