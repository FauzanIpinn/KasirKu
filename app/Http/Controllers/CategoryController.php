<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
use App\Models\category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = Category::latest()->get();

        return response()->json([
            'success' => true,
            'messege' => 'List Data Kategori',
            'data' => $categories
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        $category = Category::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'messege' => 'Kategori Berhasil Ditambahkan',
            'data' => $category
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'success' => true,
            'messege' => 'Detail Data Kategori',
            'data' => $category
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'messege' => 'Kategori Berhasil Diperbarui',
            'data' => $category
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'success' => true,
            'messege' => 'Kategori Berhasil Dihapus',
        ], 200);
    }
}
