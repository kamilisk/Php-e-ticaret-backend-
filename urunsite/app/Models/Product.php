<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'color',
        'size',
        'stock',
        'status',
        'category_id',
        'brand_id',
        ]; // Tablo sütun isimlerine göre ekle

}
