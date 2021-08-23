<?php

namespace App\Repositories\Admin\Posts;

interface AdminPostRepositoryInterface
{
    public function posts();

    public function checkPost($request, $postId);
}
