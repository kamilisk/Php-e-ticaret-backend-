<?php

namespace App\Http\Controllers;
use App\Models\Product;
use App\Helpers\ApiResponse;
use App\Http\Resources\ProductResource;
use App\Jobs\ProductJob;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        try{
            $page = $request->get('page', 1);
            // Cache key'i oluştur
            $cachekey = 'products/' . $page;
            $products = Cache::remember($cachekey, 1800, function () use ($request) {
                return Product::paginate(4);
            });

            return ApiResponse::success('Ürünler alındı', ProductResource::collection($products));
        }
        catch (\Exception $exception){
            return ApiResponse::error("Ürünler alınırken hata oluştu", $exception->getMessage());
        }
    }
    public function show($id)
    {
        $cachekey = 'products/' . $id;
        // Veriyi cache'ten çekmeye çalış
        $product = Cache::remember($cachekey,1800, function () use ($id) {
            // Cache'te yoksa, veritabanından çek
            return $this->productService->getProduct($id);
        });
        if (!$product) {
            return ApiResponse::error('Ürün bulunamadı', null, 404);
        }
        return ApiResponse::success('Ürün bulundu', new ProductResource($product));
    }

    public function store(Request $request)
    {/*
        try {
            $product = $this->productService->createProduct($request);
            return ApiResponse::success('Product created successfully', new ProductResource($product), 201);
        } catch (\Exception $exception) {
            return ApiResponse::error("Error creating product: " . $exception->getMessage());
        }
    */
        try {// Eğer hemen sonuç döndürmek istiyorsan
            if ($request->get('immediate', false))
            {
                $product = $this->productService->createProduct($request);
                return ApiResponse::success('Ürün başarıyla oluşturuldu', new ProductResource($product), 201);
            }
            // Queue'ya job ekle
            $job = new ProductJob('create', $request->all());
            dispatch($job);// Job dispatch edildi mesajı dön - product resource değil!
            return ApiResponse::success('ürün oluşturma başarıyla kuyruğa alındı', null, 202);
        } catch (\Exception $exception)
        {
            return ApiResponse::error("Ürün oluşturulurken hata meydana geldi " . $exception->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
       /* try {
            $product = $this->productService->updateProduct($request, $id);
            return ApiResponse::success('Product updated successfully', new ProductResource($product));
        } catch (\Exception $exception) {
            return ApiResponse::error("Error updating product: " . $exception->getMessage());
        }
       */
        try {
            // Eğer hemen sonuç döndürmek istiyorsan
            if ($request->get('immediate', false)) {
                $product = $this->productService->updateProduct($request, $id);
                return ApiResponse::success('Ürün başarıyla güncellendi', new ProductResource($product));
            }

            // Aksi halde, queue'ya job ekle
            $job = new ProductJob('update', $request->all(), $id);
            dispatch($job);

            // Job dispatch edildi mesajı dön
            return ApiResponse::success('ürün güncelleme başarıyla kuyruğa alındı', null, 202);
        } catch (\Exception $exception) {
            return ApiResponse::error("Ürün güncellenirken hata oluştu " . $exception->getMessage());
        }
    }

    public function destroy($id)
    {
            try {
                // Eğer hemen sonuç döndürmek istiyorsanız
                if (request()->get('immediate', false)) {
                    $deletedId = $this->productService->deleteProduct($id);
                    // İşlem tamamlandığı için başarılı bir yanıt döndürülüyor
                    return ApiResponse::success('Ürün başarıyla silindi', ['deleted_id' => $deletedId]);
                }

                // Aksi halde, queue'ya job ekle
                $job = new ProductJob('delete', ['product_id' => $id]);
                dispatch($job);

                // Job dispatch edildi mesajı dön
                return ApiResponse::success('Ürün silme başarıyla kuyruğa alındı', null, 202);
            } catch (\Exception $exception) {
                return ApiResponse::error("Error deleting product: " . $exception->getMessage());
            }
    }
}
/* try {
            $deletedId = $this->productService->deleteProduct($id);
            return ApiResponse::success('Ürün başarıyla silindi', ['deleted_id' => $deletedId]);
        }
        catch (\Exception $exception)
        {
            return ApiResponse::error("Ürün silinirken hata oluştu: " . $exception->getMessage());
        }
       */
