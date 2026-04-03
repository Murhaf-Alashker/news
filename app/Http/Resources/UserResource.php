<?php

namespace App\Http\Resources;

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
        $user = Auth::guard('api-user')->user();
        $image = Storage::disk('public')->get('upload/users/'.$this->id.$this->image) ?? null;
        $for_user = [
            'id' => $this->ulid ?? $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country' => $this->country,
            'city' => $this->city,
            'image' => $image,

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
        return $user && $user->id != $this->id ? $for_user : array_merge($for_user, $more_info);
    }
}
