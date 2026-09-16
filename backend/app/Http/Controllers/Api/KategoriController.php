<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    // GET /api/kategori
    public function index()
    {
        $kategoris = Kategori::all();
        return response()->json([
            'status' => 'success',
            'data' => $kategoris
        ], 200);
    }

    // POST /api/kategori
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $kategori
        ], 201);
    }

    // GET /api/kategori/{id}
    public function show($id)
    {
        $kategori = Kategori::findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $kategori
        ], 200);
    }

    // PUT/PATCH /api/kategori/{id}
    public function update(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $kategori
        ], 200);
    }

    // DELETE /api/kategori/{id}
    public function destroy($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alats()->count() > 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.'
            ], 400);
        }

        $kategori->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil dihapus.'
        ], 200);
    }
}