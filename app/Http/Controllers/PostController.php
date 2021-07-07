<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
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
    public function store(Request $request)
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
    public function update(Request $request, $id)
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
            $posts = Post::with('user:id,name')->orderBy('id', 'DESC')->get();

            return response()->json([
                'posts' => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
