<?php

namespace App\Services;


use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Scopes\ActivePostScope;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class UserService
{
    public static string $FILE_PATH = 'uploads/users/';
    /**
     * Create a new class instance.
     */
    public function show(User $user): UserResource
    {
        $currentUser = Auth::guard('api-user')->user();
        if($currentUser?->id !== $user->id) {
            return $this->showProfileForUsers($user);
        }
        return $this->showProfileForOwnerOrAdmin($user);
    }

    public function index(?bool $status):AnonymousResourceCollection
    {
        return UserResource::collection(
            User::withCount(['posts', 'comments'])
                ->when(!is_null($status), fn ($q) => $q->where('status', $status))
                ->paginate(10));
    }

    public function showProfileForUsers(User $user): UserResource
    {
        return new UserResource($user->loadCount(['posts','comments']));

    }
    public function showProfileForOwnerOrAdmin(?User $user = null): UserResource
    {
        if(!$user) {
            $user = Auth::guard('api-user')->user();
        }
        return new UserResource($user->loadCount(['posts','comments']));

    }

    public function update(array $data):JsonResponse
    {
        $user = Auth::guard('api-user')->user();
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'country' => $data['country'],
            'city' => $data['city'],
            'phone' => $data['phone'],
        ]);
        return response()->json([
            'message' => 'User information updated successfully',
            'user' => new UserResource($user)
        ]);
    }

    public function getUserPostsByType(User $user):AnonymousResourceCollection
    {
        $ownerOrAdmin = false;
        $user_id = Auth::guard('api-user')->id();
        if(($user_id && $user_id == $user->id) || Auth::guard('api-admin')->check()){
            $ownerOrAdmin = true;
        }
        return $this->getUserPosts($user, $ownerOrAdmin);
    }

    public function getUserPosts(User $user, bool $isOwnerOrAdmin): AnonymousResourceCollection
    {
        return PostResource::collection($user
            ->posts()
            ->tap(function ($query) use ($isOwnerOrAdmin) {
                return $isOwnerOrAdmin ? $query->withoutGlobalScope(ActivePostScope::class) : $query;
            })
            ->latest()
            ->paginate(15));
    }

    public function changeImage(Request $request):JsonResponse
    {
        $user = Auth::guard('api-user')->user();

        Storage::disk('public')->deleteDirectory(self::$FILE_PATH.$user->id);

        $newPath = null;

        if($request->hasFile('image')) {
            $media = $request->input('image');
            $extension = $media->getClientOriginalExtension();
            $newPath = Str::ulid().'.'.$extension;
            $media->storeAs(self::$FILE_PATH.$user->id,$newPath, 'public');
        }
        $user->image = $newPath;

        return response()->json([
            'message' => 'image updated successfully'
        ]);
    }

}
