<?php

namespace App\Repositories\Admin\Comments;

interface AdminCommentRepositoryInterface
{
    public function getPostComments($postId);

    public function deleteComment($postId, $commentId);
}
