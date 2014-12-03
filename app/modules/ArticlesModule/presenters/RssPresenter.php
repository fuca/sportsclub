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

namespace App\ArticlesModule\Presenters;

use \App\SystemModule\Presenters\SystemPublicPresenter;

/**
 * Rss presenter of articles module
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class RssPresenter extends SystemPublicPresenter {
    
    /**
     * @var RssControl 
     */
    private $rssControl;

    /**
     * @inject
     * @var \App\ArticlesModule\Components\RssControl\IRssModel
     */
    public $model;
    
    /**
     * Rss section from module configuration
     * @var array $rssPropertiesConfig
     */
    private $rssPropertiesConfig;
    
    public function getRssPropertiesConfig() {
	return $this->rssPropertiesConfig;
    }

    public function setRssPropertiesConfig(array $rssPropertiesConfig) {
	$this->rssPropertiesConfig = $rssPropertiesConfig;
    }

    public function getRss() {
	if (!isset($this->rssControl)) {
	    $this->rssControl = $this->getComponent('rss');
	}
	return $this->rssControl;
    }

    public function setRss(\RssControl $value) {
	$this->rssControl = $value;
    }

    public function renderDefault() {

	$this->setLayout(FALSE);

	$scrUri = filter_input(INPUT_SERVER, "SCRIPT_URI");
	$slashOff = substr($scrUri, 0, strrpos($scrUri, '/'));
	$appUrl = substr($slashOff, 0, strrpos($slashOff, '/'));
	// properties
	$this->rss->setChannelProperty('title', $this->getRssPropertiesConfig()["title"]);
	$this->rss->setChannelProperty('description', $this->getRssPropertiesConfig()["description"]);
	$this->rss->setChannelProperty('link', $appUrl);
	$this->rss->setChannelProperty("category", $this->getRssPropertiesConfig()["category"]);
	$this->rss->setChannelProperty("language", $this->getTranslator()->getLocale());
	$this->rss->setChannelProperty("copyright", $this->getRssPropertiesConfig()["copyright"]);
	$this->rss->setChannelProperty('managingEditor', $this->getRssPropertiesConfig()["managingEditor"]);
	$this->rss->setChannelProperty('webmaster', $this->getRssPropertiesConfig()["webmaster"]);
	$this->rss->setChannelProperty("lastBuildDate", date('r', time()));

	$items = $this->model->getNews();

	$its = array();
	foreach ($items as $item) {
	    $tmp = array();
	    
	    $tmp["link"] = $appUrl . $this->link(":Articles:Public:showArticle", $item->getAlias());
	    $tmp["title"] = $item->getTitle();
	    $tmp["category"] = $this->getRssPropertiesConfig()["category"];
	    array_push($its, $tmp);
	}

	$this->getRss()->setItems($its);
    }

}
