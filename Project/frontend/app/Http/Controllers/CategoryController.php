<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('categories.index', ['categories' => $categories]);
    }

    public function show($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return view('categories.show', ['category' => $category]);
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->createCategory($request->validated());
        return redirect()->route('categories.index')->with('success', 'Danh mục đã được tạo');
    }

    public function update(UpdateCategoryRequest $request, $id)
    {
        $category = $this->categoryService->updateCategory($id, $request->validated());
        return redirect()->route('categories.index')->with('success', 'Danh mục đã được cập nhật');
    }

    public function destroy($id)
    {
        $this->categoryService->deleteCategory($id);
        return redirect()->route('categories.index')->with('success', 'Danh mục đã được xóa');
    }

    public function apiIndex()
    {
        $categories = $this->categoryService->getAllCategories();
        return response()->json(CategoryResource::collection($categories));
    }

    public function apiShow($id)
    {
        $category = $this->categoryService->getCategoryById($id);
        return response()->json(new CategoryResource($category));
    }
}
