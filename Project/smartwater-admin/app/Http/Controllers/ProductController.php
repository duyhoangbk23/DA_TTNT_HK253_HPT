<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        $categories = Category::all();

        return view('products.index', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function show($id)
    {
        $product = $this->productService->getProductById($id);
        return view('products.show', ['product' => $product]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được tạo');
    }

    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->updateProduct($id, $request->validated());
        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được cập nhật');
    }

    public function destroy($id)
    {
        $this->productService->deleteProduct($id);
        return redirect()->route('products.index')->with('success', 'Sản phẩm đã được xóa');
    }

    public function apiIndex()
    {
        $products = $this->productService->getAllProducts();
        return response()->json(ProductResource::collection($products));
    }

    public function apiShow($id)
    {
        $product = $this->productService->getProductById($id);
        return response()->json(new ProductResource($product));
    }

    public function apiStore(StoreProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        return response()->json(new ProductResource($product), 201);
    }

    public function apiUpdate(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->updateProduct($id, $request->validated());
        return response()->json(new ProductResource($product));
    }

    public function apiDestroy($id)
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted'], 200);
    }
}
