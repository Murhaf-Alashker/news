<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    /**
     * Create a new class instance.
     */
    public static string $FILE_PATH = 'uploads/s/';

    public function getComments(Post $post): LengthAwarePaginator
    {
        return $post
            ->comments()
            ->where('status',1)
            ->with('user')
            ->latest()
            ->paginate(10);
    }

    public function store(string $commentText ,Post $post):Comment
    {
        return $post->comments()->create([
            'comment' => $commentText,
            'user_id' => Auth::guard('api-user')->id(),
        ]);
    }

    public function update(string $commentText,Comment $comment):Comment
    {
        $comment->update(['comment' => $commentText]);
        return $comment->load('user');
    }

    public function destroy(Comment $comment):JsonResponse
    {
        $comment->delete();
        return response()->json(['message' => 'Comment deleted successfully']);
    }

    public function ChangeStatus(Comment $comment):JsonResponse
    {
        $comment->update(['status' => !$comment->status]);
        return response()->json(['message' => 'Comment status updated successfully']);
    }

}
