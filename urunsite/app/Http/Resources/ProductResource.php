<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
            'color' => $this->color,
            'size'  => $this->size,
            'status' => $this->status,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
    public static function getDatabaseFields()
    {
        return [
            'id',
            'name',
            'price',
            'color',
            'size',
            'stock',
            'status',
            'category_id',
            'brand_id',
            'created_at',
            'updated_at',
        ];
    }
}
