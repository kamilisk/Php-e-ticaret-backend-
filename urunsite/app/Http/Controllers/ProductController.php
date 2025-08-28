<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {

        try {
            $data = $this->productService->getProducts($request);
            return ApiResponse::success('Products retrieved successfully', $data);
        } catch (\Exception $exception) {
            return ApiResponse::error("Error retrieving products: " . $exception->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $product = $this->productService->getProduct($id);
            return ApiResponse::success('Product found successfully', new ProductResource($product));
        } catch (\Exception $exception) {
            return ApiResponse::error("Product not found: " . $exception->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $product = $this->productService->createProduct($request);
            return ApiResponse::success('Product created successfully', new ProductResource($product), 201);
        } catch (\Exception $exception) {
            return ApiResponse::error("Error creating product: " . $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $product = $this->productService->updateProduct($request, $id);
            return ApiResponse::success('Product updated successfully', new ProductResource($product));
        } catch (\Exception $exception) {
            return ApiResponse::error("Error updating product: " . $exception->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $deletedId = $this->productService->deleteProduct($id);
            return ApiResponse::success('Product deleted successfully', ['deleted_id' => $deletedId]);
        } catch (\Exception $exception) {
            return ApiResponse::error("Error deleting product: " . $exception->getMessage());
        }
    }
}
