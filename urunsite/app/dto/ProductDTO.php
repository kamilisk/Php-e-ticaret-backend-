<?php

namespace App\dto;

use Illuminate\Http\Request;

class ProductDTO
{
    public string $name;
    public float $price;
    public int $stock;

    public static function fromRequest(Request $request): self
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);
        $dto = new self();
        $dto->name  = $data['name'];
        $dto->price = $data['price'];
        $dto->stock = $data['stock'];

        return $dto;
    }
    // Model create/update için array dönüşümü
    public function toArray(): array
    {
        return [
            'name'  => $this->name,
            'price' => $this->price,
            'stock' => $this->stock,
        ];
    }
}
