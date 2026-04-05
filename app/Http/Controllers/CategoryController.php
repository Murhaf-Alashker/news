<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct()
    {
        $this->categoryService = new CategoryService();
    }
    public function index(): AnonymousResourceCollection
    {
        return CategoryResource::collection($this->categoryService->index());
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        return $this->categoryService->store($request->validated());
    }

    public function update(CategoryRequest $request,Category $category): JsonResponse
    {
        return $this->categoryService->update($request->validated(), $category);
    }

    public function changeStatus(Category $category): JsonResponse
    {
        return $this->categoryService->changeStatus($category);
    }

}
