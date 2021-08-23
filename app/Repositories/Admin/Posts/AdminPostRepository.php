<?php

namespace App\Repositories\Admin\Posts;

use App\Models\Post;
use App\Notifications\CheckPost;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class AdminPostRepository implements AdminPostRepositoryInterface
{
    public function posts(): Collection
    {
        if (Gate::allows('post_access')) {
            return Post::with('user:id,name')->orderBy('id', 'DESC')->get();
        }
    }

    public function checkPost($request, $postId): object
    {
        if (Gate::allows('post_check')) {
            $post = Post::find($postId);

            if (!$post) {
                throw new \Exception('Post does not exist');
            }

            $post->update([
                'checked' => $request->checked ? 1 : 0,
            ]);

            $post->user->notify(new CheckPost($post->id, $post->title, $post->checked));

            return $post;
        }
    }
}
