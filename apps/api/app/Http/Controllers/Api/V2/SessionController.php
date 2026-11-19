<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        // Campus-scoped: a chain must never leak sessions between sites.
        return Session::query()
            ->where('campus_id', $request->user()->campus_id)
            ->with(['teacher', 'room'])
            ->paginate();
    }

    public function markAttendance(Request $request, Session $session)
    {
        // Attendance is append-only. Corrections arrive as new rows so the
        // offline sync from the teacher app can never rewrite history.
        $session->attendance()->create($request->validated());
        return response()->json(['status' => 'recorded'], 201);
    }
}
