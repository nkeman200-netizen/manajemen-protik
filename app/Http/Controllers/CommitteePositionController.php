<?php

namespace App\Http\Controllers;

use App\Models\CommitteePosition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Validation\Rule;

class CommitteePositionController extends Controller
{
    /**
     * Menampilkan daftar semua jabatan kepanitiaan.
     */
    public function index(): JsonResponse
    {
        try {
            $positions = CommitteePosition::orderBy('name', 'asc')->get();
            return response()->json([
                'success' => true,
                'data'    => $positions
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data jabatan kepanitiaan.'
            ], 500);
        }
    }

    /**
     * Menyimpan jabatan baru ke database.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255|unique:committee_positions,name',
            'is_bph' => 'required|boolean',
        ]);

        try {
            $position = CommitteePosition::create($validated);
            return response()->json([
                'success' => true,
                'data'    => $position,
                'message' => 'Jabatan kepanitiaan berhasil ditambahkan.'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.'
            ], 500);
        }
    }

    /**
     * Memperbarui data jabatan spesifik.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $position = CommitteePosition::find($id);

        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Data jabatan tidak ditemukan.'
            ], 404);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('committee_positions')->ignore($position->id),
            ],
            'is_bph' => 'required|boolean',
        ]);

        try {
            $position->update($validated);
            return response()->json([
                'success' => true,
                'data'    => $position,
                'message' => 'Data jabatan berhasil diperbarui.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui data.'
            ], 500);
        }
    }

    /**
     * Menghapus jabatan dari database.
     */
    public function destroy($id): JsonResponse
    {
        $position = CommitteePosition::find($id);

        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Data jabatan tidak ditemukan.'
            ], 404);
        }

        try {
            $position->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data jabatan berhasil dihapus.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }
}
