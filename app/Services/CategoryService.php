<?php

namespace App\Services;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class CategoryService
{
    /**
     * Create a new class instance.
     */
    public function index(): AnonymousResourceCollection
    {
        return Auth::guard('api-user')->check() ? $this->indexForUsers() : $this->indexForAdmin();
    }

    private function indexForUsers(): AnonymousResourceCollection
    {
        return CategoryResource::collection(Category::with([
            'posts' =>
                function ($query) {
                    return $query->latest()->take(3);
                }
        ])
            ->whereHas('posts')
            ->inRandomOrder()
            ->get()
        );
    }

    private function indexForAdmin():AnonymousResourceCollection
    {
        return CategoryResource::collection(
            Category::withCount('posts')
            ->withCount('posts.comments')
            ->withSum('posts', 'likes')
            ->withSum('posts', 'dislikes')
            ->withSum('posts', 'views')
            ->paginate(15)
        );
    }

    public function onlyNameAndSlug()
    {
        return CategoryResource::collection(
            Category::get()
        );
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
