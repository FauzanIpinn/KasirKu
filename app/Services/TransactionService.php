<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TransactionService
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    public function processCheckout(array $items, $userId)
    {
        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $detailsToInsert = [];

            foreach ($items as $item) {
                $product = $this->productRepository->getProductWithLock($item['product_id']);

                if ($product->stock < $item['kuantitas']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Sisa: {$product->stock}");
                }

                $subtotal = $product->price * $item['kuantitas'];
                $totalPrice += $subtotal;

                $this->productRepository->decrementStock($product, $item['kuantitas']);

                $detailsToInsert[] = [
                    'product_id' => $product->id,
                    'kuantitas'  => $item['kuantitas'],
                    'subtotal'   => $subtotal,
                ];
            }

            $transaction = Transaction::create([
                'user_id'        => $userId,
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'total_price'    => $totalPrice,
            ]);

            $transaction->details()->createMany($detailsToInsert);

            DB::commit();

            return $transaction->load('details.product', 'user');

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e; 
        }
    }
}