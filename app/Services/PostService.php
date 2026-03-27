<?php

namespace App\Services;

use App\Models\Post;

class PostService
{
    public function latestPosts()
    {
        return Post::latest()->paginate(10);
    }

    public function mostPopularPosts()
    {
        return Post::withCount('comments')->orderBy('comments_count', 'desc')->paginate(10);
    }

    public function show(Post $post): Post
    {
        return $post->load('comments');
    }

    public function store(array $data): Post
    {
        $post = Post::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'user_id' => auth()->guard('api-user')->id(),
            'category_id' => $data['category_id'],
        ]);
        //save images and videos

        //we will return it using resource
        return $post->load('media');
    }

    public function update(array $data, Post $post): Post
    {
        $post->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'],
        ]);

        //delete unwanted the media
        //save images and videos

        //we will return it using resource
        return $post;

    }

    public function destroy(Post $post): bool
    {
        //delete media
        return $post->delete();

    }

}
