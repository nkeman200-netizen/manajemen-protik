<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Finance;
use Illuminate\Validation\ValidationException;

class FinanceService
{
    public function storeFinance(array $data): Finance
    {
        $data['amount'] = ($data['qty'] ?? 1) * ($data['unit_price'] ?? 0);

        if ($data['type'] === 'expense' && !empty($data['event_id'])) {
            $event = Event::findOrFail($data['event_id']);

            $totalExistingExpense = Finance::where('event_id', $event->id)
                ->where('type', 'expense')
                ->sum('amount');

            $projectedTotal = $totalExistingExpense + $data['amount'];

            if ($projectedTotal > $event->budget_approved) {
                throw ValidationException::withMessages([
                    'amount' => 'Pengeluaran melebihi anggaran yang disetujui.',
                ]);
            }
        }

        return Finance::create($data);
    }
}
