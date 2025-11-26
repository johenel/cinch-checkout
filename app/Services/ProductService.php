<?php

namespace App\Services;

use App\Models\Product;

class ProductService extends ResourceService
{

    public function __construct(Product $product = null)
    {
        $this->resource = $product;
    }

    public function reduceStock(int $quantity): void
    {
        $this->resource->stock = $this->resource->stock - $quantity;
        $this->resource->save();
    }
}
