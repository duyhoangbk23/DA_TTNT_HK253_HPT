<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;

class ProductService
{
    public function getAllProducts()
    {
        return Product::with('category')->get();
    }

    public function getProductById($id)
    {
        return Product::with('category')->findOrFail($id);
    }

    public function createProduct(array $data)
    {
        return Product::create([
            'product_code' => $data['code'],
            'product_name' => $data['name'],
            'category_id' => $data['category_id'],
            'model' => $data['model'],
            'capacity' => $data['capacity'],
            'unit' => $data['unit'] ?? 'Chiếc',
            'price' => $data['price'],
            'status' => $data['status'],
            'image_path' => $data['image_path'] ?? null,
        ]);
    }

    public function updateProduct($id, array $data)
    {
        $product = Product::findOrFail($id);
        $product->update([
            'product_code' => $data['code'],
            'product_name' => $data['name'],
            'category_id' => $data['category_id'],
            'model' => $data['model'],
            'capacity' => $data['capacity'],
            'unit' => $data['unit'] ?? 'Chiếc',
            'price' => $data['price'],
            'status' => $data['status'],
            'image_path' => $data['image_path'] ?? null,
        ]);
        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }

    public function getProductsByCategory($categoryId)
    {
        return Product::where('category_id', $categoryId)->get();
    }
}
