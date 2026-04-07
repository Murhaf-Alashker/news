<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;


class UserController extends Controller
{
    protected UserService $userService;
    public function __construct()
    {
        $this->userService = new UserService();
    }
    public function login(Request $request)
    {
        $user = User::where('email','=',$request->input('email'))->first();
        if(!$user->password == Hash::make($request->input('password'))){
            return response()->json(['error' => 'Wrong password'], 401);
        }
        $token = $user->createToken('user_token',['api-user'])->plainTextToken;
        return response()->json(['user' => $user,'token' => $token], 200);
    }

    public function showProfile(User $user): UserResource
    {
        return $this->userService->show($user);
    }

    public function showProfileForOwner(): UserResource
    {
        return $this->userService->showProfileForOwnerOrAdmin();
    }

    public function getUserPosts(User $user):LengthAwarePaginator
    {
        return $this->userService->getUserPostsByType($user);
    }

    public function update(UserRequest $request, User $user):JsonResponse
    {
        if(Auth::guard('api-user')->id() != $user->id){
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->userService->update($request->validated());
    }

    public function pinOrUnpinUser(User $user):JsonResponse
    {
        $status = $user->status;
        $user->update([
            'status' => !$user->status
        ]);
        return response()->json(['message'=> 'user now is '.$status ? 'pined' : 'unpinned'], 200);
    }

    public function changeImage(Request $request):JsonResponse
    {
        return $this->userService->changeImage($request);
    }
}
