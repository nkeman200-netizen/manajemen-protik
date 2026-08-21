<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinanceResource;
use App\Models\Finance;
use App\Services\FinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FinanceController extends Controller
{
    public function __construct(
        private readonly FinanceService $financeService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $finances = Finance::with(['user', 'event'])
            ->when($request->search, fn ($q, $search) =>
                $q->where('description', 'like', "%{$search}%")
            )
            ->when($request->type, fn ($q, $type) =>
                $q->where('type', $type)
            )
            ->when($request->event_id, fn ($q, $eventId) =>
                $q->where('event_id', $eventId)
            )
            ->when($request->start_date && $request->end_date, fn ($q) =>
                $q->whereBetween('date', [$request->start_date, $request->end_date])
            )
            ->latest('date')
            ->paginate(15);

        return FinanceResource::collection($finances);
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorizeEventAccess($request->user(), $request->event_id, ['Ketua', 'Bendahara']);

        $validated = $request->validate([
            'user_id'        => ['required', 'exists:users,id'],
            'event_id'       => ['nullable', 'exists:events,id'],
            'type'           => ['required', 'in:income,expense'],
            'funding_source' => ['nullable', 'in:IOM,DIPA,KAS,SPONSOR'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'description'    => ['required', 'string'],
            'receipt_url'    => ['nullable', 'string'],
            'date'           => ['required', 'date'],
        ]);

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
            'funding_source' => ['nullable', 'in:IOM,DIPA,KAS,SPONSOR'],
            'amount'         => ['required', 'numeric', 'min:1'],
            'description'    => ['required', 'string'],
            'receipt_url'    => ['nullable', 'string'],
            'date'           => ['required', 'date'],
        ]);

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
}
