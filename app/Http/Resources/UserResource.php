<?php

namespace App\Http\Resources;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /**
        *@var User|null $user
        */
        $user = Auth::guard('api-user')->user();
        $is_admin = Auth::guard('api-admin')->check();
        $image = $this->image ?Storage::disk('public')->url(UserService::$FILE_PATH.$this->id.$this->image) : null;
        $for_user = [
            'id' => $this->ulid ?? $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country' => $this->country,
            'city' => $this->city,
            'image' => $image,
            'posts' => PostResource::collection($this->whenLoaded('posts')),

        ];
        $more_info = [
            'email' => $this->email,
            'status' => $this->status ? 'active' : 'inactive',
            'created_at' => $this->created_at
        ];

        if ($this->phone)
        {
            $for_user['phone'] = $this->phone;
        }
        return $is_admin || $user?->id == $this->id ? array_merge($for_user, $more_info) : $for_user;
    }
}
