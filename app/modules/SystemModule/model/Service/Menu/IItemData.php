<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace App\SystemModule\Model\Service\Menu;

/**
 *
 * @author fuca
 */
interface IItemData {
    
    public function setLabel($label);

    public function setUrl($url);

    public function setMode($mode);

    public function setData($data);

    /**
     * Data getter
     * @return mixed
     */
    public function getData();

    /**
     * Url getter.
     * @return string
     */
    public function getUrl();

    /**
     * Url getter.
     * @return string
     */
    public function getMode();

    /**
     * Label getter.
     * @return string
     */
    public function getLabel();
    
    public function getName();

    public function setName($name);
}
