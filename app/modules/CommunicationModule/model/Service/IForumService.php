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

namespace App\CommunicationModule\Model\Service;

use \App\Model\Entities\Forum,
    \App\Model\Entities\ForumThread,
    \App\Model\Entities\SportGroup,
    \App\Model\Entities\Comment,
    \App\SystemModule\Model\Service\ICommentable;

/**
 * Interface for Event service
 *
 * @author Michal Fučík <michal.fuca.fucik(at)gmail.com>.
 */
interface IForumService {

    /**
     * @param Forum $f
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createForum(Forum $f);

    /**
     * @param Forum $f
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function updateForum(Forum $f);

    /**
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteForum($id);

    /**
     * @param numeric $id
     * @param boolean $useCache
     * @return Forum
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function getForum($id);

    /**
     * Finds forum associated with given abbreviation
     * @param string $alias
     * @return Forum
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getForumAlias($alias);

    /**
     * Fetches all forums belonging into given group
     * @param SportGroup $g
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getForums(SportGroup $g);

    /**
     * Returns associative array of Forums and their ids
     * @param numeric $id
     * @return array
     * @throws Exceptions\DataErrorException
     */
    function getSelectForums($id = null);

    /**
     * Creates datasource for grid
     * @return Doctrine
     */
    function getForumDatasource();

    /**
     * @param ForumThread $t
     * @throws Exceptions\DuplicateEntryException
     * @throws Exceptions\DataErrorException
     */
    function createForumThread(ForumThread $t);

    /**
     * @param numeric $id
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function deleteForumThread($id);

    /**
     * @param numeric $id
     * @param boolean $useCache
     * @return ForumThread
     * @throws Exceptions\NullPointerException
     * @throws Exceptions\DataErrorException
     */
    function getForumThread($id, $useCache = true);

    /**
     * @param string $alias
     * @return ForumThread
     * @throws Exceptions\InvalidArgumentException
     * @throws Exceptions\DataErrorException
     */
    function getForumThreadAlias($alias);

    /**
     * @param ForumThread $f
     * @throws Exceptions\DataErrorException
     */
    function updateForumThread(ForumThread $f);

    /**
     * Creates datasource for grid
     * @return Doctrine
     */
    function getForumThreadsDataSource();

    function createComment(Comment $c, ICommentable $e);

    function updateComment(Comment $c, ICommentable $e);

    function deleteComment(Comment $c, ICommentable $e);

}
