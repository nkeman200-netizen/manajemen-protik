<?php

namespace App\Http\Controllers;

use App\Http\Resources\AgendaResource;
use App\Models\Agenda;
use App\Models\AgendaTarget;
use App\Models\Event;
use App\Models\Setting;
use App\Services\SyncService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class AgendaController extends Controller
{
    public function __construct(private readonly SyncService $syncService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $eventId   = $request->input('event_id');
        $search    = $request->input('search');
        $location  = $request->input('location_filter');
        $status    = $request->input('status_filter');
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');

        $agendas = Agenda::with(['attendances', 'targets'])
            ->when($eventId, fn($q) => $q->where('event_id', $eventId), fn($q) => $q->whereNull('event_id'))
            ->when($search, fn($q) => $q->where('title', 'like', "%{$search}%"))
            ->when($location, fn($q) => $q->where('location', 'like', "%{$location}%"))
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($startDate && $endDate, fn($q) => $q->whereBetween('start_date', [
                    $startDate . ' 00:00:00', $endDate . ' 23:59:59'
                ])
            )
            ->orderBy('start_date', 'asc')
            ->paginate(15);

        return AgendaResource::collection($agendas);
    }

    public function filters(Request $request): JsonResponse
    {
        $eventId = $request->query('event_id');
        $query   = Agenda::when($eventId, fn($q) => $q->where('event_id', $eventId), fn($q) => $q->whereNull('event_id'));

        return response()->json([
            'data' => [
                'locations' => (clone $query)->whereNotNull('location')->where('location', '!=', '')->distinct()->pluck('location'),
                'statuses'  => (clone $query)->whereNotNull('status')->where('status', '!=', '')->distinct()->pluck('status'),
            ]
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $eventId = $request->input('event_id');

        if ($eventId) {
            $event = Event::find($eventId);
            if (!$event || empty($event->agenda_sync_url)) {
                return response()->json(['message' => 'URL Sinkronisasi Agenda untuk Event ini belum diatur.'], 400);
            }
            $url = $event->agenda_sync_url;
        } else {
            $url = Setting::where('key', 'bph_agenda_sync_url')->value('value');
            if (!$url) return response()->json(['message' => 'URL Sinkronisasi Agenda BPH Pusat belum dikonfigurasi di Pengaturan.'], 500);
        }

        try {
            return response()->json($this->syncService->syncAgendas($eventId, $url));
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal menyinkronisasi data agenda.', 'error' => $e->getMessage()], 500);
        }
    }

    public function setTargets(Request $request, $id): JsonResponse
    {
        $agenda = Agenda::findOrFail($id);
        
        $validated = $request->validate([
            'targets'          => 'required|array',
            'targets.*.type'   => 'required|in:all,bph,coordinator,division,position,user',
            'targets.*.value'  => 'nullable|string',
        ]);

        DB::transaction(function () use ($agenda, $validated) {
            // Hapus target lama
            $agenda->targets()->delete();
            
            // Masukkan target baru
            foreach ($validated['targets'] as $t) {
                AgendaTarget::create([
                    'agenda_id'    => $agenda->id,
                    'target_type'  => $t['type'],
                    'target_value' => $t['value'],
                ]);
            }
        });

        return response()->json(['message' => 'Target peserta agenda berhasil diperbarui.', 'data' => $agenda->targets]);
    }
}
