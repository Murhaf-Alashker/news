<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostController extends Controller
{
    use AuthorizesRequests;
    protected PostService $postService;
    public function __construct()
    {
        $this->postService = new PostService();
    }

//    public function index()
//    {
//        return $this->postService->index();
//    }
    public function store(PostRequest $request)
    {
        $this->authorize('create', Post::class);
        return $this->postService->store($request->validated());
    }

    public function update(PostRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        return $this->postService->update($request->validated(), $post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $this->postService->destroy($post);
        return response()->json(['message' => 'Post deleted successfully.']);
    }
}
