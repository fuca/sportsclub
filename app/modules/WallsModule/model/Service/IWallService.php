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

    /**
     * Creates WallPost database entry
     * @param WallPost $w
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function createWallPost(WallPost $w);

    /**
     * Updates database state of given entity
     * @param WallPost $w
     * @return WallPost
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function updateWallPost(WallPost $w);

    /**
     * Removes WallPost entry with given id
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function removeWallPost($id);

    /**
     * Fetches entity identified by given id
     * @param numeric $id
     * @param boolean $useCache
     * @return WallPost
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getWallPost($id, $useCache = true);

    /**
     * Gets wallposts associated with given group
     * @param SportGroup $g
     * @param boolean $highlight
     * @param  WallPostStatus$status
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getWallPosts(SportGroup $g, $highlight = false, $published = null);

    /**
     * Returns array of highlighted wallpost for announcements
     * @param SportGroup $g
     * @param string $rootAbbr
     * @param boolean $published
     * @return WallPost
     * @throws Exceptions\DataErrorException
     */
    function getHighlights(SportGroup $g = null, $rootAbbr = null, $published = null);

    /**
     * Creates WallPosts datasource for Grid
     * @return Doctrine
     */
    function getWallPostsDatasource();

    /**
     * Returns array of WallPosts for displaying within wall's history control
     * @param SportGroup $g
     * @param WallPostStatus $status
     * @return WallPost
     * @throws Exceptions\DataErrorException
     */
    function getOldWallPosts(SportGroup $g, $status = WallPostStatus::PUBLISHED);
}
