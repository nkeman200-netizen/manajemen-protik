<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Finance;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class FinanceController extends Controller
{
    public function store(Request $request): JsonResponse
    {
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

        if ($validated['type'] === 'expense' && !empty($validated['event_id'])) {
            $event = Event::findOrFail($validated['event_id']);

            $totalExistingExpense = Finance::where('event_id', $event->id)
                ->where('type', 'expense')
                ->sum('amount');

            $projectedTotal = $totalExistingExpense + $validated['amount'];

            if ($projectedTotal > $event->budget_approved) {
                throw ValidationException::withMessages([
                    'amount' => 'Pengeluaran melebihi anggaran yang disetujui.',
                ]);
            }
        }

        $finance = Finance::create($validated);

        return response()->json($finance, 201);
    }
}
