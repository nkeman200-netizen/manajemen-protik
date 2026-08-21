<?php

namespace App\Http\Controllers;

use App\Http\Resources\WarningResource;
use App\Models\Warning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WarningController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $warnings = Warning::with(['user', 'admin'])
            // INJEKSI LOGIKA PENCARIAN DI SINI
            ->when($request->search, fn ($q, $search) =>
                $q->where(fn ($query) =>
                    $query->where('reason', 'like', "%{$search}%")
                          ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                )
            )
            ->when($request->user()->hasRole('member'), fn ($q) =>
                $q->where('user_id', $request->user()->id)
            )
            ->latest('date')
            ->paginate(15);

        return WarningResource::collection($warnings);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id'  => ['required', 'exists:users,id'],
            'admin_id' => ['required', 'exists:users,id'],
            'reason'   => ['required', 'string'],
            'date'     => ['required', 'date'],
        ]);

        $warning = Warning::create($validated);

        return response()->json(['message' => 'Success', 'data' => new WarningResource($warning)], 201);
    }

    public function update(Request $request, Warning $warning): JsonResponse
    {
        $validated = $request->validate([
            'user_id'  => ['required', 'exists:users,id'],
            'admin_id' => ['required', 'exists:users,id'],
            'reason'   => ['required', 'string'],
            'date'     => ['required', 'date'],
        ]);

        $warning->update($validated);

        return response()->json([
            'message' => 'Success',
            'data'    => new WarningResource($warning->load(['user', 'admin'])),
        ]);
    }

    public function destroy(Warning $warning): JsonResponse
    {
        $warning->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
