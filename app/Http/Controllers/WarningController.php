<?php
namespace App\Http\Controllers;

use App\Models\Warning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarningController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $warnings = Warning::with(['user', 'admin'])
            ->when($request->user()->hasRole('member'), fn ($q) =>
                $q->where('user_id', $request->user()->id)
            )
            ->latest('date')
            ->paginate(15);

        return response()->json($warnings);
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

        return response()->json(['message' => 'Success', 'data' => $warning], 201);
    }
}
