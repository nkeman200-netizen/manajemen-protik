<?php
namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(): JsonResponse
    {
        $documents = Document::with(['creator', 'event'])->latest()->get();

        return response()->json(['message' => 'Success', 'data' => $documents]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'created_by'    => ['required', 'exists:users,id'],
            'event_id'      => ['nullable', 'exists:events,id'],
            'letter_number' => ['required', 'string', 'max:255', 'unique:documents,letter_number'],
            'title'         => ['required', 'string', 'max:255'],
            'drive_url'     => ['required', 'string', 'max:255'],
        ]);

        $document = Document::create($validated);

        return response()->json(['message' => 'Success', 'data' => $document], 201);
    }
}
