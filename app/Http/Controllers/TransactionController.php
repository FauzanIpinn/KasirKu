<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TransactionService;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

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

        try {
            $transaction = $this->transactionService->processCheckout(
                $request->items, 
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi Berhasil',
                'data' => $transaction
            ], 201);

        } catch (\Exception $e) {
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