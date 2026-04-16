<?php

namespace App\Services;

use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Interact;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    public static string $FILE_PATH = 'uploads/posts/';
    public function latestPosts():Collection
    {
        return Post::loadActiveCommentsCount()->latest()->take(7)->get();
    }

    public function mostPopularPosts():Collection
    {
        return Post::orderByPopularCommentsCount()
            ->loadActiveCommentsCount()
            ->with('category')
            ->take(5)
            ->get();
    }

    public function featuredPosts():Collection
    {
        return Post::loadActiveCommentsCount()
            ->with('category')
            ->where('is_featured',1)
            ->latest()
            ->take(4)
            ->get();
    }

    public function mostViewedPosts():Collection
    {
        return Post::loadActiveCommentsCount()
            ->with('category')
            ->orderBy('views', 'desc')
            ->take(5)
            ->get();
    }

    public function mostLikedPosts():Collection
    {
        return Post::loadActiveCommentsCount()
            ->with('category')
            ->orderBy('likes', 'desc')
            ->take(5)
            ->get();
    }

    public function show(Post $post): JsonResponse
    {
        $post->increment('views');

        return response()->json([
            'mainPost' => $this->showPost($post),
            'relatedPosts' => $this->relatedPosts($post),
        ]);
    }

    public function store(array $data): Post
    {
        $post = Post::create([
            'ulid' => Str::ulid(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'user_id' => auth()->guard('api-user')->id(),
            'category_id' => Category::where('slug', $data['category'])->first()->id,
        ]);

        if(array_key_exists('media',$data)){
            $this->saveMedia($data['media'],$post);
        }

        //we will return it using resource
        return $post;
    }

    public function update(array $data, Post $post): PostResource
    {
        $post->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'category_id' => $data['category_id'],
        ]);

        //delete unwanted the media
        $this->deleteUnwantedMedia($post ,$data['wanted_media'] ?? []);
        //save images and videos
        if(array_key_exists('media',$data)){
            $this->saveMedia($data['media'],$post);
        }


        //we will return it using resource
        return new PostResource($post->fresh());

    }

    public function destroy(Post $post): bool
    {
        //delete media
        Storage::disk('public')->deleteDirectory(self::$FILE_PATH.$post->id);
        return $post->delete();

    }

    public function interact(string $type, Post $post): JsonResponse
    {
        $id = Auth::guard('api-user')->id();
        $interact = $post->interacts()
            ->where('user_id', $id)
            ->first();

        if (!$interact) {
            return $this->createInteract($id,$type,$post);
        }

        if ($interact->type === $type) {
            return $this->deleteInteract($interact,$type,$post);
        }

        return $this->updateInteract($interact,$type,$post);
    }

    public function changePostStatus(Post $post):JsonResponse
    {
        $post->update([
            'status' => !$post->status
        ]);
        return response()->json(['message'=>'Post Status Changed to '.$post->status ? 'active' : 'inactive'. ' Successfully']);
    }

    public function changeFeatureStatus(Post $post): JsonResponse
    {
        $post->update([
            'is_featured' => !$post->is_featured
        ]);

        return response()->json(['message' => 'the post is ' . $post->is_featured ? '' : 'not' . 'featured now']);
    }

    public function changeCommentAbility(Post $post): JsonResponse
    {
        $post->update([
            'commentable' => !$post->commentable
        ]);

        return response()->json(['message' => 'the users are'.$post->commentable ? '' : 'not'.' able to write comments on this post now']);
    }

    public function showPost(Post $post): PostResource
    {
        return new PostResource
        (
            $post
                ->loadCount([
                    'comments' => fn($q) => $q->where('status',1)
                ])
                ->load([
                    'comments' => function ($query) {
                        return $query->with('user')
                            ->where('status', 1)
                            ->orderByRaw('CASE WHEN user_id = ? THEN 0 ELSE 1 END', [Auth::guard('api-user')->id()])
                            ->orderByDesc('created_at')
                            ->take(3);
                    },
                    'user',
                    'category',
                    'media'
                ])
        );
    }

    public function relatedPosts(Post $post): AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::where('id','!=',$post->id)
                ->where('category_id','=',$post->category_id)
                ->loadActiveCommentsCount()
                ->latest()
                ->take(3)
                ->get()
        );
    }

    private function saveMedia(array $allMedia,Post $post):void
    {
        foreach ($allMedia as $media){
            $extension = $media->getClientOriginalExtension();
            $name = Str::ulid().'.'.$extension;
            $type = $extension == 'mp4' ? 'video' : 'image';
            $media->storeAs(self::$FILE_PATH.$post->id.'/'.$type,$name, 'public');
            $post->media()->create([
                'type' => $type,
                'file_name' => $name
            ]);
        }
    }

    private function deleteUnwantedMedia(Post $post ,array $wanted_media):void
    {
        $media_to_delete = $post->media()->whereNotIn('id',$wanted_media)->get();
        if(!empty($media_to_delete))
        {
            $this->deleteMedia($media_to_delete,$post);
        }
        $post->media()->whereNotIn('id',$wanted_media)->delete();
    }

    private function deleteMedia(Collection $media_to_delete,Post $post):void
    {
        foreach($media_to_delete as $media){
            Storage::disk('public')->delete(self::$FILE_PATH.$post->id.'/'.$media->type.'/'.$media->file_name);
        }
    }

    private function createInteract($userId,string $type,Post $post):JsonResponse
    {
        $post->interacts()->create([
            'user_id' => $userId,
            'type' => $type,
        ]);
        $post->increment($type);
        return response()->json([
            'message' => 'Interaction created',
            'current_interaction' => $type,
        ]);
    }

    private function deleteInteract(Interact $interact,string $type, Post $post):JsonResponse
    {
        $post->decrement($type);
        $interact->delete();
        return response()->json([
            'message' => 'Interaction removed',
            'current_interaction' => null,
        ]);
    }

    private function updateInteract(Interact $interact,string $type, Post $post):JsonResponse
    {
        $interact->update([
            'type' => $type,
        ]);

        $post->decrement($type == 'like' ? 'like' : 'dislike');
        $post->increment($type);

        return response()->json([
            'message' => 'Interaction updated',
            'current_interaction' => $type,
        ]);
    }

}
