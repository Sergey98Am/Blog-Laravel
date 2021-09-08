<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\UserAddComment;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use App\Repositories\Comments\CommentRepository;
use Illuminate\Support\Facades\Notification;

class CommentController extends Controller
{
    private $repository;

    public function __construct(CommentRepository $repository)
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

    public function store(CommentRequest $request, $postId)
    {
        try {
            $comment = $this->repository->createComment($request, $postId);

            return response()->json([
                'comment' => $comment,
                'message' => 'Comment successfully created'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function reply(CommentRequest $request, $postId, $commentId)
    {
        try {
            $comment = $this->repository->createReply($request, $postId, $commentId);

            return response()->json([
                'comment' => $comment,
                'message' => 'Comment successfully created'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(Request $request, $postId, $commentId)
    {
        try {
            $comment = $this->repository->updateComment($request, $postId, $commentId);

            return response()->json([
                'comment' => $comment,
                'message' => 'Comment successfully updated'
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

    public function loadMoreComments(Request $request, $postId)
    {
        try {
            $moreComments = $this->repository->loadComments($request, $postId);

            return response()->json([
                'comments' => $moreComments
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function loadMoreReplies(Request $request, $postId, $commentId)
    {
        try {
            $moreReplies = $this->repository->loadReplies($request, $postId, $commentId);

            return response()->json([
                'replies' => $moreReplies
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
