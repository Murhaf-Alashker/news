<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::tap(
            function ($query) {
                if (Auth::guard('api-user')->check()) {
                    return $query->where('status',1);
                }
                return $query;
            })
            ->paginate(10);
    }
}
