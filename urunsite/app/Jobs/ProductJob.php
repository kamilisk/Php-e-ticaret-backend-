<?php

// 1. ProductJob.php - Delete işlemi eklendi
namespace App\Jobs;

use App\Services\ProductService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProductJob implements ShouldQueue
{
    use Queueable;

    public $action;
    public $productData;
    public $productId;

    public function __construct($action,$productData, $productId = null)
    {
        $this->action = $action;
        $this->productData = $productData;
        $this->productId = $productId;
    }

    public function handle(ProductService $productService): void
    {
        if ($this->action === 'create') {
            $productService->createProduct($this->productData);
        } elseif ($this->action === 'update') {
            $productService->updateProduct($this->productData, $this->productId);
        } elseif ($this->action === 'delete') {
            // Delete işleminde $this->productData aslında ID'dir
            $productService->deleteProduct($this->productData);
        }
    }
}
