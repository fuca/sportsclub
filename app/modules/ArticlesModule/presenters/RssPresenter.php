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

use \App\SystemModule\Presenters\SystemPublicPresenter,
    \App\ArticlesModule\Components\RssControl\IRssModel;

/**
 * Rss presenter of articles module
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
final class RssPresenter extends SystemPublicPresenter {
    
    /**
     *  @var RssControl 
     */
    private $rssControl;

    /**
     * @inject
     * @var \App\ArticlesModule\Components\RssControl\IRssModel
     */
    private $model;

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

	// properties
	$this->rss->setChannelProperty('title', "TJ Sokol Moravičany");
	$this->rss->setChannelProperty('description', "Webová prezentace Tělovýchovné jednoty Sokol Moravičany.");
	$this->rss->setChannelProperty('link', 'http://www.sokolmoravicany.cz');
	$this->rss->setChannelProperty("category", "aktuality,články,novinky,RSS");
	$this->rss->setChannelProperty("language", "cs");
	$this->rss->setChannelProperty("copyright", "TJ Sokol Moravičany");
	$this->rss->setChannelProperty('managingEditor', "editor@sokolmoravicany.cz");
	$this->rss->setChannelProperty('webmaster', "webmaster@sokolmoravicany.cz");
	$this->rss->setChannelProperty("lastBuildDate", date('r', time()));

	$items = $this->model->getNews();

	$its = array();
	foreach ($items as $item) {
	    $tmp = array();
	    $tmp["link"] = 'http://www.sokolmoravicany.cz' . $this->link(":Front:Article:show", $item['article_id']);
	    $tmp["title"] = $item['article_title'];
	    $tmp["category"] = "články,aktuality,novinky";
	    array_push($its, $tmp);
	}

	$this->getRss()->setItems($its);
    }

}
