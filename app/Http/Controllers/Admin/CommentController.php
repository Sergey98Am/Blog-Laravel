<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Admin\Comments\AdminCommentRepository;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private $repository;

    public function __construct(AdminCommentRepository $repository)
    {
        $this->repository = $repository;
    }


    public function index($postId)
    {
        try {
            $postWithComments = $this->repository->getPostComments($postId);

            return response()->json([
                'comments' => $postWithComments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function destroy($postId, $commentId)
    {
        try {
            $comment = $this->repository->deleteComment($postId, $commentId);

            return response()->json([
                'comment' => $comment,
                'message' => 'Comment successfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
