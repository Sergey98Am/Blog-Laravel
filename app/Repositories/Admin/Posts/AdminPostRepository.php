<?php

namespace App\Repositories\Admin\Posts;

use App\Models\Post;
use App\Models\User;
use App\Notifications\CheckPost;
use App\Notifications\UserEditPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;

class AdminPostRepository implements AdminPostRepositoryInterface
{
    public $user;
    public $path;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->path = public_path() . '/images/';
    }

    public function posts(): LengthAwarePaginator
    {
        if (Gate::allows('post_access')) {
            return Post::with('user:id,name')->orderBy('id', 'DESC')->paginate(8);
        }
    }

    public function updatePost($request, $postId): object
    {
        if (Gate::allows('post_edit')) {
            $post = Post::find($postId);

            if (!$post) {
                throw new \Exception("Post doesn't exist");
            }

            if ($request->hasFile('image')) {
                \File::delete($this->path . $post->image);
                $file = $request->file('image');
                $file_name = time() . '_' . $file->getClientOriginalName();
                $file->move($this->path, $file_name);
                $post->image = $file_name;
            }

            $post->update([
                'title' => $request->title,
                'description' => $request->description,
                'checked' => false,
                'edited' => true,
            ]);

            return $post;
        }
    }

    public function deletePost($postId): object
    {
        if (Gate::allows('post_delete')) {
            $post = Post::find($postId);

            \File::delete($this->path . $post->image);
            $post->delete();

            return $post;
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
