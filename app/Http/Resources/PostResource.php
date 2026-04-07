<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
         * @var User|null $user
         */
        $user = Auth::guard('api-user')->user();
        $is_admin = Auth::guard('api-admin')->check();
        $media = $this->media()->get()->groupBy('type');
        $images = [];
        $videos = [];

        foreach ($media['image'] ?? [] as $image) {
            $images[] = ['id' => $image->id, 'url' => Storage::disk('public')->url(PostService::$FILE_PATH.$this->id.'/image/'.$image->file_name)];
        }

        foreach ($media['video'] ?? [] as $video) {
            $videos[] = ['id' => $video->id, 'url' => Storage::disk('public')->url(PostService::$FILE_PATH.$this->id.'/video/'.$video->file_name)];
        }
        $more_info = [
            'is_featured' => $this->is_featured,
            'status' => $this->status ,
            'updated_at' => $this->updated_at,
        ];

        $for_user = [
            'status' => $this->status ,
            'id' => $this->ulid,
            'views' => $this->views,
            'likes' => $this->likes,
            'dislikes' => $this->dislikes,
            'title' => $this->title,
            'description' => $this->description,
            'commentable' => $this->commentable,
            'created_at' => $this->created_at,
            'user' => $this->whenLoaded('user',fn() => new UserResource($this->user)),
            'category' => $this->whenLoaded('category', fn() => new CategoryResource($this->category)),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            'images' => $images,
            'videos' => $videos,
        ];

        if($this->comments_count ?? null){
            $for_user['comments_count'] = $this->comments_count;
        }
        return $is_admin || $user?->id == $this->user_id ? array_merge($for_user, $more_info) : $for_user;
    }
}
