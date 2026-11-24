<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function getProductWithLock($productId)
    {
        return Product::lockForUpdate()->find($productId);
    }

    public function decrementStock(Product $product, int $kuantitas)
    {
        return $product->decrement('stock', $kuantitas);
    }
}