<?php

namespace App\Repositories\Posts;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class PostRepository implements PostRepositoryInterface
{
    public $user;
    public $path;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->path = public_path() . '/images/';
    }

    public function getMyPosts(): Collection
    {

        return Post::orderBy('id', 'DESC')->where('user_id', $this->user->id)->get();
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
        ]);

        if (!$post) {
            throw new \Exception('Something went wrong');
        }

        return $post;
    }

    public function updatePost($request, $postId): object
    {
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

    public function deletePost($postId): object
    {
        $post = Post::find($postId);

        \File::delete($this->path . $post->image);
        $post->delete();

        return $post;
    }

    public function allPosts(): Collection
    {
        $posts = Post::with([
            'user:id,name',
            'likes',
        ])->orderBy('id', 'DESC')->where('checked', true)->get();

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
}
