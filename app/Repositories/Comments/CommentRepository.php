<?php

namespace App\Repositories\Comments;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Notifications\UserAddComment;
use App\Notifications\UserAddReply;
use App\Notifications\UserEditComment;
use App\Notifications\UserEditReply;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class CommentRepository implements CommentRepositoryInterface
{
    public $user;
    public $admins;

    public function __construct()
    {
        $this->user = Auth::user();
        $this->admins = User::whereHas('role', function ($query) {
            $query->whereHas('permissions', function ($query) {
                $query->where('title', 'comment_access');
            });
        })->get();
    }

    public function getPostComments($postId): object
    {
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

    public function createComment($request, $postId): object
    {
        $comment = Post::find($postId)->comments()->create([
            'comment' => $request->comment,
            'post_id' => $postId,
            'user_id' => $this->user->id
        ]);

        if (!$comment) {
            throw new \Exception('Something went wrong');
        }

        $parameters = [
            0 => $comment->user->name,
            1 => $comment->post->id,
            2 => $comment->id,
            3 => $comment->comment,
            4 => ''
        ];
        $commentPostId = $comment->post->id;
        $url = "/admin/post/$commentPostId";
        $parameters[4] = $url;
        Notification::send($this->admins, new UserAddComment(...$parameters));

        $comment->limitedReplies = $comment->replies()->with('user')->limit(10)->get();

        return $comment->load('user');
    }

    public function createReply($request, $postId, $commentId): object
    {
        $comment = Comment::find($commentId)->create([
            'comment' => $request->comment,
            'parent_id' => $commentId,
            'post_id' => $postId,
            'user_id' => $this->user->id,
        ]);

        if (!$comment) {
            throw new \Exception('Something went wrong');
        }

        $commentParentId = $comment->parent_id;
        $parentComment = Comment::find($commentParentId);
        $parameters = [
            0 => $comment->user->name,
            1 => $comment->post->id,
            2 => $comment->parent_id,
            3 => $comment->id,
            4 => $parentComment->comment,
            5 => ''
        ];

        $commentPostId = $comment->post->id;
        $url = "/admin/post/$commentPostId";
        $parameters[5] = $url;
        Notification::send($this->admins, new UserAddReply(...$parameters));

        $url = "/post/$commentPostId";
        $parameters[5] = $url;
        $parentComment->user->notify(new UserAddReply(...$parameters));

        return $comment->load('user');
    }

    public function updateComment($request, $postId, $commentId)
    {
        $comment = Comment::find($commentId);

        if (!$comment) {
            throw new \Exception('Comment does not exist');
        }

        if ($request->comment == '') {
            $request->comment = $comment->comment;
        }

        $comment->update([
            'comment' => $request->comment,
        ]);

        return $comment;
    }

    public function deleteComment($postId, $commentId): object
    {
        $comment = Comment::find($commentId);

        if (!$comment) {
            throw new \Exception('Comment does not exist');
        }

        $comment->delete();

        return $comment;
    }

    public function loadComments($request, $postId)
    {
        $post = Post::find($postId);

        if ($post) {
            $moreComments = $post->comments()->with('user')->where('id', '<', $request->last_id)->orderBy('id', 'DESC')->limit(10)->get();
            foreach ($moreComments as $comment) {
                $comment->limitedReplies = $comment->replies()->with('user')->limit(10)->get();
            }
        }

        return $moreComments ?? null;
    }

    public function loadReplies($request, $postId, $commentId)
    {
        $comment = Comment::find($commentId);

        if ($comment) {
            $moreReplies = $comment->replies()->with('user')->where('id', '>', $request->last_id)->limit(10)->get();
        }

        return $moreReplies ?? null;
    }
}
