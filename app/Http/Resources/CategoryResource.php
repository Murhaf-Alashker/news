<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $info = [
            'name' => $this->name,
            'slug' => $this->slug,
            'posts' => PostResource::collection($this->whenLoaded('posts')),

        ];
        if(Auth::guard('api-admin')->check()) {
            $info['status'] = $this->status ? 'active' : 'inactive';
            $info['comments_count'] = $this->posts_comments_count ?? 0;
            $info['posts_count'] = $this->posts_count ?? 0;
            $info['posts_sum_views'] = $this->posts_sum_views ?? 0;
            $info['posts_sum_likes'] = $this->posts_sum_likes ?? 0;
            $info['posts_sum_dislikes'] = $this->posts_sum_likes ?? 0;
        }
        return $info;
    }
}
