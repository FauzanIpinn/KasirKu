<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    public function checkout(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.kuantitas' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $detailsToInsert = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

                if ($product->stock < $item['kuantitas']) {
                    throw new \Exception("Stok untuk produk '{$product->name}' tidak mencukupi. Sisa: {$product->stock}");
                }
 
                $subtotal = $product->price * $item['kuantitas'];
                $totalPrice += $subtotal;

                $product->decrement('stock', $item['kuantitas']);

                $detailsToInsert[] = [
                    'product_id' => $product->id,
                    'kuantitas' => $item['kuantitas'],
                    'subtotal' => $subtotal,
                ];
            }

            $transaction = Transaction::create([
                'user_id' => auth()->id(),
                'invoice_number' => 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'total_price' => $totalPrice,
            ]);

            $transaction->details()->createMany($detailsToInsert);

            DB::commit();

            $transaction->load('details.product', 'user');

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil',
                'data' => $transaction
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function index(): JsonResponse
    {
        $transactions = Transaction::with('user', 'details.product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Riwayat Transaksi',
            'data' => $transactions
        ], 200);
    }

    public function show(string $id): JsonResponse
    {
        $transaction = Transaction::with('user', 'details.product')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Detail Transaksi',
            'data' => $transaction
        ], 200);
    }
}