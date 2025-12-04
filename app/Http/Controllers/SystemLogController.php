<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $roles = $user->roles->pluck('name')->toArray();

        // hanya super_admin & tim_monev yang boleh lihat log
        if (! in_array('super_admin', $roles) && ! in_array('tim_monev', $roles)) {
            abort(403);
        }

        $query = SystemLog::with('user:id,name,username')
            ->orderByDesc('created_at');

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('search')) {
            $query->where('description', 'ilike', '%' . $request->search . '%');
        }

        $logs = $query->paginate(20)->withQueryString();

        // Kalau FE JS nanti minta JSON (Accept: application/json)
        if ($request->wantsJson()) {
            return response()->json($logs);
        }

        // Untuk sekarang: pakai Blade tester
        return view('logs.index', [
            'logs'    => $logs,
            'filters' => $request->only(['action', 'search']),
        ]);
    }
}
