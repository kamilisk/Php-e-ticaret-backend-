<?php

namespace App\Services;

use App\dto\ProductDTO;
use App\Filters\ProductFilter;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductService
{
    public function getProducts(Request $request)
    {
        $perPage = $request->get('per_page', 4);
        $page = (int) $request->get('page', 1);

        $filterParams = $request->only(ProductFilter::getFilterableFields());
        $cacheKey = "products_page_{$page}_{$perPage}_" . md5(serialize($filterParams));

        $products = Cache::remember($cacheKey, 1800, function () use ($perPage, $request) {
            $filter = new ProductFilter($request);
            $query = Product::query();
            $filteredQuery = $filter->apply($query);
            return $filteredQuery->paginate($perPage);
        });

        return [
            'products' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages()
            ],
            'filters_applied' => array_filter($filterParams)
        ];
    }

    public function getProduct($id)
    {
        $cacheKey = 'product_' . $id;
        return Cache::remember($cacheKey, 1800, function () use ($id) {
            return Product::findOrFail($id);
        });
    }

    public function createProduct(Request $request)
    {
        $dto = ProductDTO::fromRequest($request);
        $product = Product::create($dto->toArray());

        $this->clearProductCaches();

        return $product;
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $dto = ProductDTO::fromRequest($request);
        $product->update($dto->toArray());

        $this->clearProductCaches();
        Cache::forget('product_' . $id);

        return $product->fresh();
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);

        Cache::forget('product_' . $id);
        $this->clearProductCaches();

        $deletedId = $product->id;
        $product->delete();

        return $deletedId;
    }

    public function getAllProductsWithoutFilters(Request $request)
    {
        $perPage = $request->get('per_page', 4);
        $products = Product::paginate($perPage);

        return [
            'products' => ProductResource::collection($products->items()),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem(),
                'has_more_pages' => $products->hasMorePages()
            ]
        ];
    }

    private function clearProductCaches()
    {
        Cache::forget('product_filter_options');
    }
}
