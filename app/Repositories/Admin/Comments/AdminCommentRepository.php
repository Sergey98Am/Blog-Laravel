<?php

namespace App\Repositories\Admin\Comments;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AdminCommentRepository implements AdminCommentRepositoryInterface
{
    public $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function getPostComments($postId): object
    {
        if (Gate::allows('comment_access')) {
            $post = Post::with([
                'comments' => function ($q) {
                    $q->with([
                        'user',
                    ])->orderBy('id', 'DESC')->limit(10)->get();
                }
            ])->find($postId);

            foreach ($post->comments as $comment) {
                $comment->limitedReplies = $comment->replies()->with('user')->limit(10)->get();
            }

            return $post;
        }
    }

    public function deleteComment($postId, $commentId): object
    {
        if (Gate::allows('comment_delete')) {
            $comment = Comment::find($commentId);

            if (!$comment) {
                throw new \Exception('Comment does not exist');
            }

            $comment->delete();

            return $comment;
        }
    }
}
