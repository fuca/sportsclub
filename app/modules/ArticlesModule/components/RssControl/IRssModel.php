<?php

namespace App\ArticlesModule\Components\RssControl;

/**
 * Interface for Rss model
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>
 */
interface IRssModel {
    
    /**
     * Returns array of news to display within rss channel
     */
    function getNews();
}
