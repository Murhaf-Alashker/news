<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\ContactUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactUsController extends Controller
{
    public function index():JsonResponse
    {
        $contacts = ContactUs::get()->with('user')->paginate(20);
        return response()->json([
            'title' => $contacts->title,
            'description' => $contacts->description,
            'user' => new UserResource($contacts->user),
        ]);
    }

    public function store(Request $request):ContactUs
    {
        $data =$request->validate([
            'title' => ['required','string','max:50','min:3'],
            'description' => ['required','string','max:255','min:3']
        ]);
        $user = Auth::guard('api-user')->user();

        return $user->contacts()->create($data);

    }
}
