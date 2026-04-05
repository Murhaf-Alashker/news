<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Http\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CategoryService;
use App\Services\PostService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;

class PostController extends Controller
{
    use AuthorizesRequests;
    protected PostService $postService;
    protected CategoryService $categoryService;
    public function __construct()
    {
        $this->postService = new PostService();
        $this->categoryService = new CategoryService();
    }

    public function homePage(): JsonResponse
    {
        return response()->json([
            'latestPosts' => PostResource::collection($this->postService->latestPosts()),
            'mostPopularPosts' => PostResource::collection($this->postService->mostPopularPosts()),
            'featuredPosts' => PostResource::collection($this->postService->featuredPosts()),
            'mostViewedPosts' => PostResource::collection($this->postService->mostViewedPosts()),
            'mostLikedPosts' => PostResource::collection($this->postService->mostLikedPosts()),
        ]);
    }

    public function show(Post $post):PostResource
    {
        return $this->postService->show($post);
    }
    public function store(PostRequest $request) :Post
    {
        $this->authorize('create', Post::class);
        return $this->postService->store($request->validated());
    }

    public function update(PostRequest $request, Post $post) :PostResource
    {
        $this->authorize('update', $post);
        return $this->postService->update($request->validated(), $post);
    }

    public function destroy(Post $post):JsonResponse
    {
        $this->authorize('delete', $post);
        $this->postService->destroy($post);
        return response()->json(['message' => 'Post deleted successfully.']);
    }



    public function interact(Request $request,Post $post): JsonResponse
    {
        $request->validate([
            'type' => ['required', 'in:like,dislike'],
        ]);

        return $this->postService->interact($request->input('type'),$post);
    }

    public function changePostStatus(Post $post):JsonResponse
    {
        return $this->postService->changePostStatus($post);
    }

    public function changeFeatureStatus(Post $post):JsonResponse
    {
        return $this->postService->changeFeatureStatus($post);
    }

    public function changeCommentAbility(Post $post):JsonResponse
    {
        return $this->postService->changeCommentAbility($post);
    }
}
