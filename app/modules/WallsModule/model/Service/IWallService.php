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

namespace App\WallsModule\Model\Service;

use \App\Model\Entities\WallPost,
    \App\Model\Entities\SportGroup,
    \App\SystemModule\Model\Service\ICommenting,
    \App\Model\Misc\Enum\WallPostStatus;

/**
 * Interface of Wall service
 * @author <michal.fuca.fucik(at)gmail.com>
 */
interface IWallService extends ICommenting {
    

    function createWallPost(WallPost $w);
    
    function updateWallPost(WallPost $w);
    
    function removeWallPost($id);
    
    function getWallPost($id);
    
    function getWallPosts(SportGroup $g, $highlight = false, $published = null);
    
    function getHighlights(SportGroup $g = null, $rootAbbr = null, $published = null);
    
    function getWallPostsDatasource();
    
    function getOldWallPosts(SportGroup $g, $status = WallPostStatus::PUBLISHED);
    
}
