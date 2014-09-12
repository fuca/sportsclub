<?php

namespace App\Model\Misc\Annotation;

use Nette\Object,
    Nette\Reflection\IAnnotation,
    Nette\Utils\Strings;

/**
 * Description of Annotation
 *
 * @author fuca
 */
abstract class Annotation extends Object implements IAnnotation {

    const VALUE_ID = "value";
    
    private $values;

    public function __construct(array $values) {
	$res = [];
	$matches = [];
	$val = $values[self::VALUE_ID];
	if (0 == preg_match("/(?:\b[a-z]+\b\s*\=\s*\{\s*\b[a-z]+(?:(?:\_|\.|\s*\,\s*)[a-z]+\b)*\s*\}\s*)/i", $val))
	    throw new \Nette\InvalidArgumentException("Syntax error within annotation parameters '{$val}'");
	preg_match_all('/\b[a-z]+\b\s*\=\s*\{\s*\b[a-z]+(?:(?:\_|\.|\s*\,\s*)[a-z]+\b)*\s*\}/mi', $val, $matches);
	foreach ($matches[0] as $param) {
	    list($k, $vs) = explode('=', $param);
	    $vs = preg_replace('/(?:\{|\}|\s)/', "", $vs);
	    $parVals = explode(',', $vs);
	    $res[Strings::lower($k)] = count($parVals) == 1 ? $parVals[0] : $parVals;
	}
	$this->values = $res;
    }
    
    public function &__get($name) {
	$res = $methodIs = $methodGet = null;
	$ref = $this->getReflection();
	$fUpper = Strings::firstUpper($name);
	$isName = "is".$fUpper;
	$getName = "get".$fUpper;
	if ($ref->hasMethod($getName))
		$methodGet = $ref->getMethod($getName);
	if ($ref->hasMethod($isName))
	    	$methodIs = $ref->getMethod($isName);
	if (($methodGet !== null && $methodGet->isPublic()) || ($methodIs !== null && $methodIs->isPublic()))
	    $res = isset($this->values[$name])?$this->values[$name]:null;
	return $res;
    }
}
