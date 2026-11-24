<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $products =  Product::with('category')->latest()->get();

        return response()->json([
            'success' => true,
            'messege' => 'List Data Produk',
            'data' => $products
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $product = product::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->stock,
        ]);

        return response()->json([
            'success' => true,
            'messege' => 'Produk Berhasil Ditambahkan',
            'data' => $product
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json([
            'success' => true,
            'messege' => 'Detail Data Produk',
            'data' => $product
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, string $id): JsonResponse
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|string|max:255',
            'price'       => 'required|integer|min:0',
            'stock'       => 'required|integer|min:0',
        ]);

        $product->update([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'price'       => $request->price,
            'stock'       => $request->stock,
        ]);

        $product->load('category');

        return response()->json([
            'success' => true,
            'message' => 'Produk Berhasil Diperbarui',
            'data'    => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk Berhasil Dihapus',
        ], 200);
    }
}
