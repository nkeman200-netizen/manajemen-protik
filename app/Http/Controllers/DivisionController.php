<?php
namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(Division::latest()->paginate(15));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:divisions,name|max:255',
        ]);
        $division = Division::create($validated);
        return response()->json(['message' => 'Divisi berhasil ditambahkan', 'data' => $division], 201);
    }

    public function update(Request $request, Division $division)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:divisions,name,' . $division->id . '|max:255',
        ]);
        $division->update($validated);
        return response()->json(['message' => 'Divisi berhasil diperbarui', 'data' => $division]);
    }

    public function destroy(Division $division)
    {
        $division->delete();
        return response()->json(['message' => 'Divisi berhasil dihapus']);
    }
}
