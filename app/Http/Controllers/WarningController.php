<?php
namespace App\Http\Controllers;

use App\Models\Warning;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WarningController extends Controller
{
    public function index(): JsonResponse
    {
        $warnings = Warning::with(['user', 'admin'])->latest('date')->get();

        return response()->json(['message' => 'Success', 'data' => $warnings]);
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
