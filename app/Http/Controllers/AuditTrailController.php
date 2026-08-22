<?php

namespace App\Http\Controllers;

use App\Models\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    public function index(Request $request)
    {
        $audits = AuditTrail::with('user')->latest()->paginate(15);
        return response()->json($audits);
    }
}
