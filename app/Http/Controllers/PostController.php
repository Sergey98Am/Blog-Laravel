<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Like;
use App\Models\Post;
use JWTAuth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $posts = Post::orderBy('id', 'DESC')->where('user_id', JWTAuth::user()->id)->get();

            return response()->json([
                'posts' => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePostRequest $request)
    {
        try {
            if ($request->has('image')) {
                $file = $request->file('image');
                $file_name = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/images/', $file_name);
            }


            $post = Post::create([
                'image' => $file_name,
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => JWTAuth::user()->id
            ]);

            if (!$post) {
                throw new \Exception('Something went wrong');
            }

            return response()->json([
                'createdPost' => $post,
                'message' => 'Post successfully created'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostRequest $request, $id)
    {
        try {
            $updatedPost = Post::find($id);

            if ($updatedPost) {
                if ($request->hasFile('image')) {
                    \File::delete(public_path() . '/images/' . $updatedPost->image);
                    $file = $request->file('image');
                    $file_name = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path() . '/images/', $file_name);
                    $updatedPost->image = $file_name;
                }

                $updatedPost->update([
                    'title' => $request->title,
                    'description' => $request->description,
                    'checked' => false,
                    'edited' => true,
                ]);

                return response()->json([
                    'updatedPost' => $updatedPost,
                    'message' => 'Post successfully updated'
                ], 200);
            } else {
                throw new \Exception('Post does not exist');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $deletedPost = Post::find($id);

            if ($deletedPost) {
                \File::delete(public_path() . '/images/' . $deletedPost->image);
                $deletedPost->delete();
                return response()->json([
                    'deletedPost' => $deletedPost,
                    'message' => 'Post successfully deleted'
                ], 200);

            } else {
                throw new \Exception('Post does not exist');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function allPosts()
    {
        try {
            $posts = Post::with([
                'user:id,name',
                'likes',
            ])->orderBy('id', 'DESC')->where('checked', true)->get();

            return response()->json([
                'posts' => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function saveLike($postId)
    {
        try {
            $like = Like::where(['post_id' => $postId, 'user_id' => JWTAuth::user()->id])->first();

            if ($like) {
                Like::where(['post_id' => $postId, 'user_id' => JWTAuth::user()->id])->delete();
                $deleted = 1;
            } else {
                $like = Like::create([
                    'post_id' => $postId,
                    'user_id' => JWTAuth::user()->id
                ]);
                $deleted = 0;
            }

            return response()->json([
                'deleted' => $deleted,
                'like' => $like
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
