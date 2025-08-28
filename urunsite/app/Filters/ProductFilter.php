<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class ProductFilter
{
    protected $request;
    protected $builder;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder)
    {
        $this->builder = $builder;

        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter) && !empty($value)) {
                $this->$filter($value);
            }
        }

        return $this->builder;
    }

    public static function getFilterableFields()
    {
        // ProductResource'tan database alanlarını al ve özel filtreleri ekle
        $baseFields = collect(ProductResource::getDatabaseFields())
            ->except(['id', 'name', 'created_at', 'updated_at']) // Bu alanları filtreden çıkar
            ->toArray();

        // Özel filtre alanlarını ekle
        $customFields = ['min_price', 'max_price', 'search'];

        return array_merge($baseFields, $customFields);
    }

    protected function getFilters()
    {
        return $this->request->only(self::getFilterableFields());
    }

    protected function color($value)
    {
        return $this->builder->where('color', 'like', '%' . $value . '%');
    }

    protected function size($value)
    {
        return $this->builder->where('size', $value);
    }

    protected function price($value)
    {
        return $this->builder->where('price', $value);
    }

    protected function min_price($value)
    {
        return $this->builder->where('price', '>=', $value);
    }

    protected function max_price($value)
    {
        return $this->builder->where('price', '<=', $value);
    }

    protected function stock($value)
    {
        return $this->builder->where('stock', '>=', $value);
    }

    protected function status($value)
    {
        return $this->builder->where('status', $value);
    }

    protected function category_id($value)
    {
        return $this->builder->where('category_id', $value);
    }

    protected function brand_id($value)
    {
        return $this->builder->where('brand_id', $value);
    }

    protected function search($value)
    {
        return $this->builder->where(function($query) use ($value) {
            $query->where('name', 'like', '%' . $value . '%');
               //DB de açıklama oldugu zaman bu aktif ->orWhere('description', 'like', '%' . $value . '%');
        });
    }
}

