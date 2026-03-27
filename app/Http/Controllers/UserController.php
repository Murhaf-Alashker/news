<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email','=',$request->input('email'))->first();
        if(!$user->password == Hash::make($request->input('password'))){
            return response()->json(['error' => 'Wrong password'], 401);
        }
        $token = $user->createToken('user_token',['api-user'])->plainTextToken;
        return response()->json(['user' => $user,'token' => $token], 200);
    }
}
