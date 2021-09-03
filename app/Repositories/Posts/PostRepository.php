<?php

namespace App\Repositories\Posts;

use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use App\Notifications\UserCreatePost;
use App\Notifications\UserEditPost;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

class PostRepository implements PostRepositoryInterface
{
    public $user;
    public $path;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->path = public_path() . '/images/';
    }

    public function getMyPosts(): LengthAwarePaginator
    {
        return Post::orderBy('id', 'DESC')->where('user_id', $this->user->id)->paginate(9);
    }

    public function createPost($request): object
    {
        if ($request->has('image')) {
            $file = $request->file('image');
            $file_name = time() . '_' . $file->getClientOriginalName();
            $file->move($this->path, $file_name);
        }

        $post = Post::create([
            'image' => $file_name,
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => $this->user->id
        ])->load('user');

        if (!$post) {
            throw new \Exception('Something went wrong');
        }

        $admins = User::whereHas('role', function ($query) {
            $query->whereHas('permissions', function ($query) {
                $query->where('title', 'post_check');
            });
        })->get();

        Notification::send($admins, new UserCreatePost($post->user->name, $post->id, $post->title));

        return $post;
    }

    public function updatePost($request, $postId): object
    {
        $post = Post::find($postId);
        $old_post_title = $post->title;

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

        $admins = User::whereHas('role', function ($query) {
            $query->whereHas('permissions', function ($query) {
                $query->where('title', 'post_check');
            });
        })->get();

        Notification::send($admins, new UserEditPost($old_post_title, $post->user->name, $post->id));

        return $post;
    }

    public function deletePost($postId): object
    {
        $post = Post::find($postId);

        \File::delete($this->path . $post->image);
        $post->delete();

        return $post;
    }

    public function allPosts(): LengthAwarePaginator
    {
        $posts = Post::with([
            'user:id,name',
            'likes',
        ])->orderBy('id', 'DESC')->where('checked', true)->paginate(9);

        return $posts;
    }

    public function saveLike($postId): array
    {
        $like = Like::where(['post_id' => $postId, 'user_id' => $this->user->id])->first();

        if ($like) {
            Like::where(['post_id' => $postId, 'user_id' => $this->user->id])->delete();
            $deleted = 1;
        } else {
            $like = Like::create([
                'post_id' => $postId,
                'user_id' => $this->user->id
            ]);
            $deleted = 0;
        }

        return [
            'deleted' => $deleted,
            'like' => $like
        ];
    }

    public function onePost($postId): object
    {
        $post = Post::with(['user:id,name'])->find($postId);

        return $post;
    }
}
