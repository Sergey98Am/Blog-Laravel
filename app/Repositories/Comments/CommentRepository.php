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
        $comment = Comment::create([
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
            2 => $comment->post->title,
            3 => $comment->id,
            4 => $comment->comment,
            5 => ''
        ];
        $commentPostId = $comment->post->id;
        $url = "/admin/post/$commentPostId";
        $parameters[5] = $url;
        Notification::send($this->admins, new UserAddComment(...$parameters));

        $userOfCommentPost = $comment->post->user()->where('id', '!=', $this->user->id)->first();
        if ($userOfCommentPost) {
            $url = "/post/$commentPostId";
            $parameters[5] = $url;
            $userOfCommentPost->notify(new UserAddComment(...$parameters));
        }

        $comment->limitedReplies = $comment->replies()->with('user')->limit(10)->get();

        return $comment->load('user');
    }

    public function createReply($request, $postId, $parentCommentId)
    {
        $parentComment = Comment::find($parentCommentId);

        if (!$parentComment) {
            throw new \Exception('Something went wrong');
        }

        $comment = Comment::create([
            'comment' => $request->comment,
            'parent_id' => $parentCommentId,
            'post_id' => $postId,
            'user_id' => $this->user->id,
        ]);

        if (!$comment) {
            throw new \Exception('Something went wrong');
        }

        $parameters = [
            0 => $comment->user->name,
            1 => $comment->post->id,
            2 => $parentComment->post->title,
            3 => $comment->parent_id,
            4 => $comment->id,
            5 => $parentComment->comment,
            6 => ''
        ];

        $parentCommentPostId = $parentComment->post->id;
        $url = "/admin/post/$parentCommentPostId";
        $parameters[6] = $url;
        Notification::send($this->admins, new UserAddReply(...$parameters));

        $notLoggedInUserOfParentCommentPost = $parentComment->post->user()->where('id', '!=', $this->user->id)->first();
        $notLoggedInUserOfParentComment = $parentComment->user()->where('id', '!=', $this->user->id)->first();
        $users = User::whereIn('id', [$notLoggedInUserOfParentCommentPost['id'], $notLoggedInUserOfParentComment['id']])->get();
        $url = "/post/$parentCommentPostId";
        $parameters[6] = $url;
        Notification::send($users, new UserAddReply(...$parameters));

        $userOfParentCommentPost = $parentComment->post->user;
        $userOfParentComment = $parentComment->user;
        $usersOfReplies = $parentComment->replies()->with('user')->get()->unique('user.id')->pluck('user');
        $usersOfRepliesFiltered = $usersOfReplies->whereNotIn('id', [$this->user->id, $userOfParentCommentPost->id, $userOfParentComment->id]);
        Notification::send($usersOfRepliesFiltered, new UserAddReply(...$parameters));

        return $comment->load('user');
    }

    public function updateComment($request, $postId, $commentId): object
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

    public function loadReplies($request, $postId, $parentCommentId)
    {
        $comment = Comment::find($parentCommentId);

        if ($comment) {
            $moreReplies = $comment->replies()->with('user')->where('id', '>', $request->last_id)->limit(10)->get();
        }

        return $moreReplies ?? null;
    }
}
