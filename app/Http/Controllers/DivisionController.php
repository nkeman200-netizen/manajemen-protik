<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(): JsonResponse
    {
        $divisions = Division::all();

        return response()->json([
            'message' => 'Success',
            'data' => $divisions,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:divisions,name'],
        ]);

        $division = Division::create($validated);

        return response()->json([
            'message' => 'Success',
            'data' => $division,
        ], 201);
    }

    public function update(Request $request, Division $division): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'unique:divisions,name,' . $division->id],
        ]);

        $division->update($validated);

        return response()->json([
            'message' => 'Success',
            'data' => $division,
        ]);
    }

    public function destroy(Division $division): JsonResponse
    {
        $division->delete();

        return response()->json([
            'message' => 'Success',
        ]);
    }
}
