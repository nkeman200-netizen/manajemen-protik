<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(): JsonResponse
    {
        try {
            $settings = Setting::all()->pluck('value', 'key');
            return response()->json(['success' => true, 'data' => $settings], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat pengaturan.'], 500);
        }
    }

    public function updateBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'settings'         => 'required|array',
            'settings.*.key'   => 'required|string|exists:settings,key',
            'settings.*.value' => 'nullable|string',
        ]);

        try {
            foreach ($validated['settings'] as $settingData) {
                Setting::where('key', $settingData['key'])->update(['value' => $settingData['value']]);
            }
            return response()->json(['success' => true, 'message' => 'Pengaturan berhasil diperbarui.'], 200);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui pengaturan.'], 500);
        }
    }

    public function uploadLogo(Request $request): JsonResponse
    {
        $request->validate([
            'logo' => 'required|file|mimes:jpeg,png,jpg,svg,webp|max:5120',
        ]);

        try {
            $file     = $request->file('logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $path     = $file->storeAs('logos', $filename, 'public');

            // Menghasilkan absolute URL (e.g. http://localhost:8000/storage/logos/...)
            $url = asset('storage/' . $path);

            Setting::updateOrCreate(
                ['key' => 'org_logo'],
                ['value' => $url, 'type' => 'file']
            );

            return response()->json([
                'success' => true,
                'message' => 'Logo berhasil diunggah.',
                'url'     => $url,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error Asli: ' . $e->getMessage() . ' | Baris: ' . $e->getLine(),
            ], 500);
        }
    }

    public function uploadTemplate(Request $request): JsonResponse
    {
        $request->validate([
            'template_type' => 'required|string|in:peminjaman_perlengkapan,peminjaman_tempat,undangan_eksternal,undangan_internal_satu,undangan_internal_banyak,permohonan_kerjasama',
            'document'      => 'required|file|mimes:docx|max:10240', // Max 10MB
        ]);

        try {
            $file     = $request->file('document');
            $fileName = $request->template_type . '.docx';

            // Simpan dan Timpa (Overwrite) file di storage/app/templates
            $file->storeAs('templates', $fileName, 'local');

            return response()->json([
                'success' => true,
                'message' => 'Template berhasil diperbarui.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah template: ' . $e->getMessage(),
            ], 500);
        }
    }
}
