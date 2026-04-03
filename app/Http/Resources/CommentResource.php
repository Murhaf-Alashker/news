<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $info = [
            'id' => $this->id,
            'comment' => $this->comment,
            'user' => new UserResource($this->user)
        ];
        if(Auth::guard('api-admin')->check()) {
            $info['status'] = $this->status ? 'active' : 'inactive';
        }
        return $info;
    }
}
