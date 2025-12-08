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

        $columns = [
            'Waktu',
            'Pengguna',
            'Aksi',
            'Deskripsi',
        ];

        $rows = $logs->map(function (SystemLog $log) {
            return [
                'id'        => $log->id,
                'waktu'     => e(optional($log->created_at)->format('d M Y H:i')),
                'pengguna'  => e(optional($log->user)->name ?? optional($log->user)->username ?? '-'),
                'aksi'      => e($log->action ?? '-'),
                'deskripsi' => e($log->description ?? '-'),
            ];  
        })->toArray();

        return view('systemlog', [
            'columns' => $columns,
            'rows'    => $rows,
        ]);

    }
}
