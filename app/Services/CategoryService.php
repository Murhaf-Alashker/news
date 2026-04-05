<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class CategoryService
{
    /**
     * Create a new class instance.
     */
    public function index(): Collection
    {
        return Category::with([
            'posts' =>
                function ($query) {
                    $q = $query->latest();
                    return Auth::guard('api-admin')->check() ?$q : $q->take(3);
                }
        ])
        ->tap(
            function ($query) {
                return Auth::guard('api-admin')->check() ? $query : $query->whereHas('posts');
            })
            ->inRandomOrder()
            ->get();
    }

    public function store(array $data): JsonResponse
    {
        return $this->createOrUpdate($data);
    }

    public function update(array $data, Category $category): JsonResponse
    {
        return $this->createOrUpdate($data,'update',$category);
    }

    public function changeStatus(Category $category): JsonResponse
    {
        $category->update([
            'status' => !$category->status
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
        ]);
    }

    private function createOrUpdate(array $data,string $method = 'create', ?Category $category = null):JsonResponse
    {
        $slug = Str::slug($data['name']);
        if(Category::where('slug',$slug)->exists()){
            return response()->json([
                'message' => 'Category already exists',
            ],400);
        }
        if ($method == 'update'){
            return $this->updateCategory($data['name'],$slug,$category);
        }
        return $this->createCategory($data['name'],$slug);
    }

    private function createCategory(string $name,string $slug):JsonResponse
    {
        $category = Category::create([
            'name' => $name,
            'slug' => $slug,
        ]);
        return response()->json([
            'message' => 'Category created successfully',
            'category' => $category,
        ],201);
    }

    private function updateCategory(string $name,string $slug,Category $category):JsonResponse
    {
        $category->update([
            'name' => $name,
            'slug' => $slug,
        ]);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => $category->fresh(),
        ],200);
    }
}
