<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinanceResource;
use App\Models\Event;
use App\Models\Finance;
use App\Services\FinanceService;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FinanceController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
        private readonly SyncService $syncService,
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
            // OPTIMASI: Menggunakan cursor() (Lazy Collection) alih-alih get() agar RAM tidak penuh
            $finances = $query->cursor();
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
        $eventId = $request->input('event_id');

        if ($eventId) {
            $event = Event::find($eventId);
            if (!$event || empty($event->finance_sync_url)) {
                return response()->json(['message' => 'URL Sinkronisasi Keuangan untuk Event ini belum diatur.'], 400);
            }
            $url = $event->finance_sync_url;
        } else {
            $url = env('TRACKING_KEUANGAN_URL');
            if (!$url) return response()->json(['message' => 'URL Sinkronisasi Keuangan BPH Pusat belum dikonfigurasi.'], 500);
        }

        try {
            return response()->json($this->syncService->syncFinances($eventId, $url, auth()->id() ?? 1));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data.', 'error' => $e->getMessage()], 500);
        }
    }
}
