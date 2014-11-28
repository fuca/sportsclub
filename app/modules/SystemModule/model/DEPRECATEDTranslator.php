<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\SystemModule\Model\Service\Localization;

use Nette\Localization\ITranslator,
    App\Model\Service\BaseService;

/**
 * RB file name is locale key in DB as well
 *
 * @author fuca
 */
class Translator extends BaseService implements ITranslator {

    const TRANSLATOR_ENTITY_NAME = "resourceBundle";
    
    // TODO rbService

    /** @var string absolute resource dir */
    private $rbRootPath;

    public function setFilePath($path) {
	if (!is_dir($path))
	    throw new \Nette\FileNotFoundException("Passed target path is not directory");
	// check if it is not empty
	$this->rbRootPath = $path;
    }

    public function __construct(\Kdyby\Doctrine\EntityManager $em) {
	parent::__construct($em, self::TRANSLATOR_ENTITY_NAME);
    }

    // vyresit jak tady rozlisovat locale = asi svoje latte makro, ktery to sem posle

    private function getFileNameFromLocale() {
	// TODO check whether locale file exist
	return "cs_CZ";
	
    }

    private function fetchFromFile($filePath) {
	if (!is_readable($filePath))
	    throw new \Nette\InvalidStateException("Cannot read $filePath");
	$res = [];
	$handle = fopen($filePath, "r");
	if ($handle) {
	    while (($line = fgets($handle)) !== false) {
		$chunks = array_map(function($el) {
		    $_el = $el;
		    if(!mb_check_encoding($_el)) 
			$_el = mb_convert_encoding($_el, mb_internal_encoding());
		    return trim($_el);
		}, explode('=', $line));
		if (count($chunks) == 2)
		    $res[$chunks[0]] = $chunks[1];
	    }
	} else {
	    throw new \Nette\DirectoryNotFoundException("Opening of $filePath failed");
	}
	fclose($handle);
	return $res;
    }

    private function fetchFromDatabase($locale) {
	$res = [];
	if ($this->bundleService == null)
	    return $res;

	$data = $this->bundleService->getData($locale);
	$res[$locale] = $data;
	return $res;
    }

    private function getData($locale) {
	$cache = $this->getEntityCache();
	$data = $cache->load($locale);
	$fName = null;
	if ($data == null) {
	    if (count(glob("$this->rbRootPath/*")) === 0)
		throw new \Nette\InvalidStateException("RB root dir is empty");
	    
	    $dit = new \DirectoryIterator($this->rbRootPath);
	    while ($dit->valid()) {
		    if ($dit->getBasename() == $locale) {
			if ($dit->isFile()) {
			    $fName = $dit->getPathName();
			    break;
			}
		    }
		$dit->next();
	    }
	    $data[] = $this->fetchFromFile($fName);
	    $data = array_merge($data, $this->fetchFromDatabase($locale));
	    $opts = ([\Nette\Caching\Cache::TAGS => [$locale]]);
	    $cache->save($locale, $data, $opts);
	}
	return $data;
    }

    public function translate($message, $count = NULL) {
	$data = $this->getData($this->getFileNameFromLocale());
	if (isset($data[$message])) {
	    return $data[$message];
	} else {
	    return $message;
	}
    }

}
