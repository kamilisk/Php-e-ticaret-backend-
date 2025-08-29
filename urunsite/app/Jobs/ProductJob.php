<?php

namespace App\Jobs;

use App\Services\ProductService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable as BusQueueable;
use Exception;
use Illuminate\Support\Facades\Log;

class ProductJob implements ShouldQueue
{
    use BusQueueable, Queueable, InteractsWithQueue, SerializesModels;

    public $tries = 3; // 3 kez dene
    public $timeout = 300; // 5 dakika timeout

    private string $operation;
    private array $data;
    private ?int $productId;

    /**
     * Job instance oluştur
     *
     * @param string $operation - 'create', 'update', 'delete'
     * @param array $data - İşlem için gerekli data
     * @param int|null $productId - Update/Delete için product ID
     */
    public function __construct(string $operation, array $data = [], ?int $productId = null)
    {
        $this->operation = $operation;
        $this->data = $data;
        $this->productId = $productId;

    }

    /**
     * Job'ı çalıştır
     */
    public function handle(ProductService $productService): void
    {
        try {
            Log::info("ProductJob started", [
                'operation' => $this->operation,
                'product_id' => $this->productId,
                'data' => $this->data
            ]);

            switch ($this->operation) {
                case 'create':
                    $this->handleCreate($productService);
                    break;

                case 'update':
                    $this->handleUpdate($productService);
                    break;

                case 'delete':
                    $this->handleDelete($productService);
                    break;

                default:
                    throw new Exception("Invalid operation: {$this->operation}");
            }

            Log::info("ProductJob completed successfully", [
                'operation' => $this->operation,
                'product_id' => $this->productId
            ]);

        } catch (Exception $exception) {
            Log::error("ProductJob failed", [
                'operation' => $this->operation,
                'product_id' => $this->productId,
                'error' => $exception->getMessage(),
                'attempt' => $this->attempts()
            ]);

            // Job başarısız olursa exception'ı yeniden fırlat
            throw $exception;
        }
    }

    /**
     * Ürün oluşturma işlemi
     */
    private function handleCreate(ProductService $productService): void
    {
        $request = new \Illuminate\Http\Request($this->data);
        $product = $productService->createProduct($request);

        Log::info("Product created via queue", [
            'product_id' => $product->id,
            'name' => $product->name ?? 'N/A'
        ]);
    }

    /**
     * Ürün güncelleme işlemi
     */
    private function handleUpdate(ProductService $productService): void
    {
        if (!$this->productId) {
            throw new Exception("Product ID is required for update operation");
        }

        $request = new \Illuminate\Http\Request($this->data);
        $product = $productService->updateProduct($request, $this->productId);

        Log::info("Product updated via queue", [
            'product_id' => $product->id,
            'name' => $product->name ?? 'N/A'
        ]);
    }

    /**
     * Ürün silme işlemi
     */
    private function handleDelete(ProductService $productService): void
    {
        if (!$this->productId) {
            throw new Exception("Product ID is required for delete operation");
        }

        $deletedId = $productService->deleteProduct($this->productId);

        Log::info("Product deleted via queue", [
            'deleted_id' => $deletedId
        ]);
    }

    /**
     * Job başarısız olduğunda çalışır
     */
    public function failed(Exception $exception): void
    {
        Log::error("ProductJob permanently failed", [
            'operation' => $this->operation,
            'product_id' => $this->productId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }

}
