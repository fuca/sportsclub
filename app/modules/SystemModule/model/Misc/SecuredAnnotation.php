<?php

use App\Model\Misc\Annotation\Annotation;

/**
 * Description of SecuredAnnotation
 *
 * @author fuca
 */
class SecuredAnnotation extends Annotation {
    
    const   PRIVILEGE_ID = "privileges",
	    RESOURCE_ID = "resource";
    
    const ANNOTATION_NAME = "Secured";
    
    public function __construct(array $values) {
	parent::__construct($values);
	if ($this->resource == null)
	    throw new \App\Model\Misc\Exceptions\InvalidArgumentException("Secured annotation has to have '".self::RESOURCE_ID."' parameter with at least one value");
	    
    }
    
    public function getPrivileges() {
	return $this->privileges;
    }

    public function getResource() {
	return $this->resource;
    }
        
    public function __toString() {
    return self::PRIVILEGE_ID."={$this->getPrivileges()},".self::RESOURCE_ID."={$this->getResource()}";
    }
}
