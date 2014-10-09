<?php

namespace App\SystemModule\Model\Service;

use 
    \App\Model\Entities\Comment,
    \App\SystemModule\Model\Service\ICommentable;
/**
 *
 * @author fuca
 */
interface ICommenting {
    
    function createComment(Comment $c, ICommentable $e);
    //function deleteComment(Comment $c, ICommentable $e);
    //function updateComment(Comment $c, ICommentable $e);
}
