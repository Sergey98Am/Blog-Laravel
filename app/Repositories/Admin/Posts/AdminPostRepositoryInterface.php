<?php

namespace App\Repositories\Admin\Posts;

interface AdminPostRepositoryInterface
{
    public function posts();

    public function updatePost($request, $postId);

    public function deletePost($post);

    public function checkPost($request, $postId);
}
