<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
            $error_status_code = 400;
            if (Gate::denies('post_access')) {
                $error_status_code = 403;
                throw new \Exception('Forbidden 403');
            }

            $posts = Post::with('user:id,name')->orderBy('id', 'DESC')->get();

            return response()->json([
                'posts' => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $error_status_code);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePostRequest $request, $id)
    {
        try {
            $error_status_code = 400;
            if (Gate::denies('post_edit')) {
                $error_status_code = 403;
                throw new \Exception('Forbidden 403');
            }

            $updatedPost = Post::find($id);

            if ($updatedPost) {
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
            ], $error_status_code);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $error_status_code = 400;
            if (Gate::denies('post_delete')) {
                $error_status_code = 403;
                throw new \Exception('Forbidden 403');
            }

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
            ], $error_status_code);
        }
    }

    public function checkPost(Request $request, $id)
    {
        try {
            $error_status_code = 400;
            if (Gate::denies('post_check')) {
                $error_status_code = 403;
                throw new \Exception('Forbidden 403');
            }

            $post = Post::find($id);

            if ($post) {
                $post->update([
                    'checked' => $request->checked ? 1 : 0,
                ]);

                return response()->json([
                    'post' => $post,
                    'message' => 'Post checked'
                ], 200);
            } else {
                throw new \Exception('Post does not exist');
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $error_status_code);
        }
    }
}
