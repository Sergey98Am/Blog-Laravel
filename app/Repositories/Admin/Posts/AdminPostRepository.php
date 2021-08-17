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

    public function updatePost($request, $postId): object
    {
        if (Gate::allows('post_edit')) {
            $post = Post::find($postId);

            if (!$post) {
                throw new \Exception('Post does not exist');
            }

            if ($request->hasFile('image')) {
                \File::delete(public_path() . '/images/' . $post->image);
                $file = $request->file('image');
                $file_name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/images/', $file_name);
                $post->image = $file_name;
            }

            $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'edited' => true,
            ]);

            return $post;
        }
    }

    public function deletePost($postId): object
    {
        if (Gate::allows('post_delete')) {
            $post = Post::find($postId);

            if (!$post) {
                throw new \Exception('Post does not exist');
            }

            \File::delete(public_path() . '/images/' . $post->image);
            $post->delete();
        }

        return $post;
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
