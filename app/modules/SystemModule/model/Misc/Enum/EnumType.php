<?php

/**
 * This piece of code is taken from http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/mysql-enums.html
 */

namespace App\Model\Misc\Enum;

use Doctrine\DBAL\Types\Type,
    Doctrine\DBAL\Platforms\AbstractPlatform,
    Nette\InvalidArgumentException;

abstract class EnumType extends Type {
    
    protected $name;
    protected $values = array();

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform) {
        $values = array_map(function($val) { return "'".$val."'"; }, $this->values);

        return "ENUM(".implode(", ", $values).") COMMENT '(DC2Type:".$this->name.")'";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform) {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform) {
        if (!in_array($value, $this->values)) {
            throw new InvalidArgumentException("Invalid '".$this->name."' value.");
        }
        return $value;
    }

    public function getName() {
        return $this->name;
    }
}