<?php

namespace App\Repositories\Comments;

interface CommentRepositoryInterface
{
    public function getPostComments($postId);

    public function createComment($request, $postId);

    public function createReply($request, $postId, $parentCommentId);

    public function updateComment($request, $postId, $commentId);

    public function deleteComment($postId, $commentId);

    public function loadComments($request, $postId);

    public function loadReplies($request, $postId, $parentCommentId);
}
