<?php

namespace App\Http\Controllers;

use App\Filters\ProductFilter;
use App\Helpers\ApiResponse;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductFilterController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Filtreleme seçeneklerini getiren endpoint
     */
    public function filterOptions()
    {
        $cacheKey = 'product_filter_options';

        $options = Cache::remember($cacheKey, 3600, function () {
            return [
                'colors' => Product::distinct('color')->whereNotNull('color')->pluck('color')->sort()->values(),
                'sizes' => Product::distinct('size')->whereNotNull('size')->pluck('size')->sort()->values(),
                'categories' => Product::distinct('category_id')->whereNotNull('category_id')->pluck('category_id')->sort()->values(),
                'brands' => Product::distinct('brand_id')->whereNotNull('brand_id')->pluck('brand_id')->sort()->values(),
                'status_options' => Product::distinct('status')->whereNotNull('status')->pluck('status')->sort()->values(),
                'price_range' => [
                    'min' => Product::min('price'),
                    'max' => Product::max('price')
                ],
                'stock_range' => [
                    'min' => Product::min('stock'),
                    'max' => Product::max('stock')
                ]
            ];
        });

        return ApiResponse::success('Filters options retrieved successfully', $options);
    }

    /**
     * Filtreleri temizleme endpoint'i
     */
    public function clearFilters(Request $request)
    {
        $data = $this->productService->getAllProductsWithoutFilters($request);

        return ApiResponse::success('Filters cleared, showing all products', $data);
    }

    /**
     * Mevcut filtreleri gösterme
     */
    public function activeFilters(Request $request)
    {
        $filterParams = $request->only(ProductFilter::getFilterableFields());
        $activeFilters = array_filter($filterParams);

        return ApiResponse::success('Active filters', [
            'active_filters' => $activeFilters,
            'filter_count' => count($activeFilters),
            'available_filters' => ProductFilter::getFilterableFields()
        ]);
    }
}
