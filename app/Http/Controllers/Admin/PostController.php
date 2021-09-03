<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\UpdatePostRequest;
use App\Repositories\Admin\Posts\AdminPostRepository;

class PostController extends Controller
{
    private $repository;

    public function __construct(AdminPostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $posts = $this->repository->posts();

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
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(\App\Http\Requests\UpdatePostRequest $request, int $postId)
    {
        try {
            $updatedPost = $this->repository->updatePost($request, $postId);

            return response()->json([
                'updatedPost' => $updatedPost,
                'message' => 'Post successfully updated'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $postId)
    {
        try {
            $deletedPost = $this->repository->deletePost($postId);

            return response()->json([
                'deletedPost' => $deletedPost,
                'message' => 'Post successfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function checkPost(Request $request, int $postId)
    {
        try {
            $post = $this->repository->checkPost($request, $postId);

            return response()->json([
                'checkPost' => $post,
                'message' => 'Post checked'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
