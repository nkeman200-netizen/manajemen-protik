<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinanceResource;
use App\Models\Finance;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {}

    public function index(Request $request)
    {
        $query = Finance::with(['user', 'event'])
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($query) =>
                    $query->where('title', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('notes', 'like', "%{$search}%")
                )
            )
            ->when($request->type, fn ($q, $type) =>
                $q->where('type', $type)
            )
            ->where('event_id', $request->input('event_id'))
            ->when($request->start_date && $request->end_date, fn ($q) =>
                $q->whereBetween('date', [$request->start_date, $request->end_date])
            )
            ->latest('date');

        // BYPASS OPTIMASI EXPORT
        if ($request->boolean('export')) {
            $finances = $query->get();
            return response()->json([
                'message' => 'Export payload ready',
                'data' => FinanceResource::collection($finances)
            ]);
        }

        $finances = $query->paginate(15);
        return FinanceResource::collection($finances);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $request->event_id, ['Ketua', 'Bendahara']);

        $validated = $request->validate([
            'user_id'        => ['required', 'exists:users,id'],
            'event_id'       => ['nullable', 'exists:events,id'],
            'type'           => ['required', 'in:income,expense'],
            'category'       => ['nullable', 'string'],
            'funding_source' => ['nullable', 'string'],
            'title'          => ['required', 'string', 'max:255'],
            'qty'            => ['required', 'numeric', 'min:0.01'],
            'unit'           => ['nullable', 'string', 'max:50'],
            'unit_price'     => ['required', 'numeric', 'min:0'],
            'pic'            => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'notes'          => ['nullable', 'string'],
            'receipt_url'    => ['nullable', 'string'],
            'date'           => ['required', 'date'],
        ]);

        $validated['description'] = $validated['title'];

        $finance = $this->financeService->storeFinance($validated);

        return (new FinanceResource($finance))
            ->response()
            ->setStatusCode(201);
    }

    public function update(Request $request, Finance $finance): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $finance->event_id, ['Ketua', 'Bendahara']);

        $validated = $request->validate([
            'event_id'       => ['nullable', 'exists:events,id'],
            'type'           => ['required', 'in:income,expense'],
            'category'       => ['nullable', 'string'],
            'funding_source' => ['nullable', 'string'],
            'title'          => ['required', 'string', 'max:255'],
            'qty'            => ['required', 'numeric', 'min:0.01'],
            'unit'           => ['nullable', 'string', 'max:50'],
            'unit_price'     => ['required', 'numeric', 'min:0'],
            'pic'            => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string'],
            'notes'          => ['nullable', 'string'],
            'receipt_url'    => ['nullable', 'string'],
            'date'           => ['required', 'date'],
        ]);

        $validated['description'] = $validated['title'];
        $validated['amount'] = ($validated['qty'] ?? 1) * ($validated['unit_price'] ?? 0);

        $finance->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => new FinanceResource($finance->load(['user', 'event'])),
        ]);
    }

    public function destroy(Request $request, Finance $finance): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $finance->event_id, ['Ketua', 'Bendahara']);

        $finance->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $url = env('TRACKING_KEUANGAN_URL');
        
        if (!$url) {
            return response()->json(['message' => 'URL Sinkronisasi Keuangan belum dikonfigurasi di .env'], 500);
        }

        $separator = str_contains($url, '?') ? '&' : '?';
        $freshUrl = $url . $separator . 'cb=' . time();

        try {
            $context = stream_context_create(['http' => ['header' => "Cache-Control: no-cache\r\n"]]);
            $csvData = file_get_contents($freshUrl, false, $context);
            $rows = array_map('str_getcsv', explode("\n", $csvData));
            
            $header = [];
            $dataStartIndex = 0;
            foreach ($rows as $index => $row) {
                // FIX: Trim semua elemen row untuk membuang spasi tak kasat mata (Hidden Space)
                $cleanRow = array_map('trim', $row);
                if (in_array('Tipe', $cleanRow) || in_array('Tipe (Pemasukan/Pengeluaran)', $cleanRow)) {
                    $header = $cleanRow;
                    $dataStartIndex = $index + 1;
                    break;
                }
            }

            if (empty($header)) {
                return response()->json(['message' => 'Format Header (Tipe, Rincian, dll) tidak ditemukan.'], 400);
            }

            $idx = [
                'tgl'      => array_search('Tanggal (YYYY-MM-DD)', $header) ?: array_search('Tanggal', $header),
                'tipe'     => array_search('Tipe (Pemasukan/Pengeluaran)', $header) ?: array_search('Tipe', $header),
                'rincian'  => array_search('Rincian', $header),
                'kategori' => array_search('Kategori', $header),
                'vol'      => array_search('Volume', $header),
                'satuan'   => array_search('Satuan', $header),
                'harga'    => array_search('Harga Satuan', $header),
                'sumber'   => array_search('Sumber Dana', $header),
                'pic'      => array_search('Penanggungjawab', $header),
                'metode'   => array_search('Metode', $header),
                'nota'     => array_search('Link Nota', $header),
                'ket'      => array_search('Keterangan', $header),
            ];

            $parseDate = function ($dateStr) {
                if (empty($dateStr) || strtolower(trim($dateStr)) === 'nan') return null;
                try {
                    $cleanDate = str_replace('/', '-', trim($dateStr));
                    return Carbon::parse($cleanDate)->format('Y-m-d');
                } catch (\Exception $e) { return null; }
            };

            $parseUrl = function ($urlStr) {
                if (empty($urlStr) || strtolower(trim($urlStr)) === 'nan') return null;
                $cleanUrl = trim($urlStr);
                if (!preg_match("~^(?:f|ht)tps?://~i", $cleanUrl)) return null;
                return filter_var($cleanUrl, FILTER_VALIDATE_URL) ? $cleanUrl : null;
            };

            // FIX: Menghapus simbol mata uang (Rp) dan pemisah ribuan
            $parsePrice = function ($priceStr) {
                if (empty($priceStr) || strtolower(trim($priceStr)) === 'nan') return 0;
                // Pisahkan koma desimal (Rp416.000,00 -> Rp416.000)
                $priceStr = explode(',', $priceStr)[0];
                $cleanPrice = preg_replace('/[^0-9]/', '', $priceStr);
                return (float) $cleanPrice;
            };

            $val = function($row, $index) {
                if ($index === false || !isset($row[$index])) return null;
                $v = trim($row[$index]);
                return (strtolower($v) === 'nan' || $v === '') ? null : $v;
            };

            DB::transaction(function () use ($rows, $dataStartIndex, $idx, $parseDate, $parseUrl, $parsePrice, $val) {
                // WIPE KAS UMUM (Event ID IS NULL)
                Finance::whereNull('event_id')->delete();

                for ($i = $dataStartIndex; $i < count($rows); $i++) {
                    $row = $rows[$i];
                    if (empty($row) || count($row) < 3) continue;

                    $rincian = $val($row, $idx['rincian']);
                    $tipeRaw = $val($row, $idx['tipe']);
                    
                    if (!$rincian || !$tipeRaw) continue;

                    $type = (stripos($tipeRaw, 'masuk') !== false || strtolower($tipeRaw) === 'income') ? 'income' : 'expense';
                    $date = $parseDate($val($row, $idx['tgl'])) ?? now()->toDateString();
                    
                    $qty = (float) ($val($row, $idx['vol']) ?? 1);
                    $price = $parsePrice($val($row, $idx['harga']));
                    $totalAmount = $qty * $price;

                    Finance::create([
                        'user_id'        => auth()->id() ?? 1,
                        'event_id'       => null,
                        'type'           => $type,
                        'category'       => $val($row, $idx['kategori']),
                        'title'          => $rincian,
                        'description'    => $rincian,
                        'qty'            => $qty,
                        'unit'           => $val($row, $idx['satuan']),
                        'unit_price'     => $price,
                        'amount'         => $totalAmount,
                        'funding_source' => $val($row, $idx['sumber']),
                        'pic'            => $val($row, $idx['pic']),
                        'payment_method' => $val($row, $idx['metode']),
                        'receipt_url'    => $parseUrl($val($row, $idx['nota'])),
                        'notes'          => $val($row, $idx['ket']),
                        'date'           => $date,
                    ]);
                }
            });

            return response()->json(['message' => "Sinkronisasi Kas Umum berhasil. Laporan keuangan telah diperbarui dari Cloud."]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data keuangan.', 'error' => $e->getMessage()], 500);
        }
    }
}
