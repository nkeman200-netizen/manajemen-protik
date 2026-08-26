<?php

namespace App\Http\Controllers;

use App\Models\Archive;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;

class ArchiveController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $archives = Archive::orderBy('period_year', 'desc')->orderBy('created_at', 'desc')->get();
            return response()->json(['success' => true, 'data' => $archives], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period_year' => 'required|string|max:50',
            'name'        => 'required|string|max:255',
            'drive_url'   => 'required|url',
        ]);

        try {
            $archive = Archive::create($validated);
            return response()->json(['success' => true, 'data' => $archive, 'message' => 'Arsip dibuat.'], 201);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal membuat arsip.'], 500);
        }
    }

    public function update(Request $request, $id): JsonResponse
    {
        $archive = Archive::find($id);
        if (!$archive) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);

        $validated = $request->validate([
            'period_year' => 'sometimes|required|string|max:50',
            'name'        => 'sometimes|required|string|max:255',
            'drive_url'   => 'sometimes|required|url',
        ]);

        try {
            $archive->update($validated);
            return response()->json(['success' => true, 'data' => $archive, 'message' => 'Arsip diperbarui.'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui.'], 500);
        }
    }

    public function destroy($id): JsonResponse
    {
        $archive = Archive::find($id);
        if (!$archive) return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);

        try {
            $archive->delete();
            return response()->json(['success' => true, 'message' => 'Arsip dihapus.'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus.'], 500);
        }
    }
}
