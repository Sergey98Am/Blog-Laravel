<?php

namespace App\Repositories\Posts;

interface PostRepositoryInterface
{
    public function getMyPosts();

    public function createPost($request);

    public function updatePost($request, $postId);

    public function deletePost($postId);

    public function allPosts();

    public function saveLike($postId);
}
