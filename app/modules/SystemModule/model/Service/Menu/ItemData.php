<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\SystemModule\Model\Service\Menu;

use Nette\Object;

/**
 * Description of MenuItemData
 *
 * @author fuca
 */
class ItemData extends Object implements IItemData {
    
    /** @var string */
    private $name;

    /** @var string */
    private $label;

    /** @var string */
    private $url;

    /** @var bool */
    private $mode;

    /** @var mixed */
    private $data;

    public function setLabel($label) {
	$this->label = $label;
    }

    public function setUrl($url) {
	$this->url = $url;
    }

    public function setMode($mode) {
	$this->mode = $mode;
    }

    public function setData($data) {
	$this->data = $data;
    }

    /**
     * Data getter
     * @return mixed
     */
    public function getData() {
	return $this->data;
    }

    /**
     * Url getter.
     * @return string
     */
    public function getUrl() {

	if (!isset($this->url))
	    $this->url = '#';

	return $this->url;
    }

    /**
     * Url getter.
     * @return string
     */
    public function getMode() {

	if (!isset($this->mode))
	    $this->mode = TRUE;

	return $this->mode;
    }

    /**
     * Label getter.
     * @return string
     */
    public function getLabel() {

	if (!isset($this->label))
	    $this->label = 'ERROR';

	return $this->label;
    }
    
    public function getName() {
	return $this->name;
    }

    public function setName($name) {
	$this->name = $name;
    }

}
