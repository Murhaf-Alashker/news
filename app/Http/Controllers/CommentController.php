<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentController extends Controller
{
    use AuthorizesRequests;
    protected CommentService $commentService;

    public function __construct()
    {
        $this->commentService = new CommentService();
    }
    public function store(CommentRequest $request, Post $post): Comment
    {
        $this->authorize('store', Comment::class);
        $data = $request->validated();
        return $this->commentService->store($data['comment'],$post);
    }

    public function getComments(Post $post): LengthAwarePaginator
    {
        return $this->commentService->getComments($post);
    }

    public function update(CommentRequest $request, Comment $comment): Comment
    {
        $this->authorize('update', $comment);
        $data = $request->validated();
        return $this->commentService->update($data['comment'],$comment);
    }

    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);
        return $this->commentService->destroy($comment);
    }

    public function changeStatus(Comment $comment): JsonResponse
    {
        return $this->commentService->changeStatus($comment);
    }
}
