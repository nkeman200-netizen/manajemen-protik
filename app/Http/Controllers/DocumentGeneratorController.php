<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentGeneratorController extends Controller
{
    public function generate(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'template_type'      => 'required|in:peminjaman_perlengkapan,peminjaman_tempat,undangan_eksternal,undangan_internal_satu,undangan_internal_banyak,permohonan_kerjasama',
            'nomor_surat'        => 'required|string',
            'lampiran'           => 'nullable|string',
            'ejaan_lampiran'     => 'nullable|string',
            'tujuan_surat'       => 'required|string',
            'nama_kegiatan'      => 'required|string',
            'detail_undangan'    => 'nullable|string',
            'bantuan'            => 'nullable|string',
            'hari_tanggal'       => 'required|string',
            'waktu_pelaksanaan'  => 'required|string',
            'tempat_pelaksanaan' => 'required|string',
        ]);

        // 2. Resolusi path template
        $templateFileName = $validated['template_type'] . '.docx';
        $templatePath     = storage_path('app/templates/' . $templateFileName);

        if (!file_exists($templatePath)) {
            return response()->json([
                'success' => false,
                'message' => "File template '{$templateFileName}' tidak ditemukan di folder storage/app/templates/.",
            ], 404);
        }

        // FIX: Deklarasikan folder temp milik Laravel secara eksplisit SEBELUM
        // TemplateProcessor diinstansiasi, agar PhpWord tidak jatuh ke C:\WINDOWS\Temp
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        \PhpOffice\PhpWord\Settings::setTempDir($tempDir);

        // 3. Injeksi variabel ke template
        $templateProcessor = new TemplateProcessor($templatePath);

        $months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $today  = date('j') . ' ' . $months[(int) date('n')] . ' ' . date('Y');

        $templateProcessor->setValue('tanggal_surat',      $today);
        $templateProcessor->setValue('nomor_surat',        $validated['nomor_surat']);
        $templateProcessor->setValue('lampiran',           $validated['lampiran'] ?? '-');
        $templateProcessor->setValue('ejaan_lampiran',     $validated['ejaan_lampiran'] ?? '-');
        $templateProcessor->setValue('tujuan_surat',       $validated['tujuan_surat']);
        $templateProcessor->setValue('nama_kegiatan',      $validated['nama_kegiatan']);
        $templateProcessor->setValue('detail_undangan',    $validated['detail_undangan'] ?? '');
        $templateProcessor->setValue('bantuan',             $validated['bantuan'] ?? '');
        $templateProcessor->setValue('hari_tanggal',       $validated['hari_tanggal']);
        $templateProcessor->setValue('waktu_pelaksanaan',  $validated['waktu_pelaksanaan']);
        $templateProcessor->setValue('tempat_pelaksanaan', $validated['tempat_pelaksanaan']);

        // 4. Siapkan nama file output dari nomor surat (sanitasi karakter terlarang OS)
        $safeFileName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], ' ', $validated['nomor_surat']);
        $safeFileName = trim(preg_replace('/\s+/', ' ', $safeFileName));
        $outputFileName = $safeFileName . '.docx';
        $outputPath     = $tempDir . '/' . $outputFileName;

        $templateProcessor->saveAs($outputPath);

        // 5. Unduh dan hapus file sementara otomatis
        return response()->download($outputPath, $outputFileName)->deleteFileAfterSend(true);
    }
}
