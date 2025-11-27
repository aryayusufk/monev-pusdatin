<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ActivityNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityNoteController extends Controller
{
    protected function ensureCanAddNote($user)
    {
        // Aturan: hanya super_admin, tim_monev, sekre_pimpinan, pimpinan
        if (! (
            $user->hasRole('super_admin') ||
            $user->hasRole('tim_monev') ||
            $user->hasRole('sekre_pimpinan') ||
            $user->hasRole('pimpinan')
        )) {
            abort(403);
        }
    }

    public function store(Request $request, Activity $activity)
    {
        Gate::authorize('view', $activity); // minimal boleh lihat dulu

        $user = $request->user();
        $this->ensureCanAddNote($user);

        $data = $request->validate([
            'note' => ['required','string','max:2000'],
        ]);

        ActivityNote::create([
            'activity_id' => $activity->id,
            'user_id'     => $user->id,
            'note'        => $data['note'],
        ]);

        return back()->with('success', 'Catatan berhasil ditambahkan.');
    }

    public function destroy(ActivityNote $note)
    {
        $user = request()->user();

        // boleh hapus jika:
        // - dia pembuat catatan, atau
        // - super_admin / tim_monev / sekre_pimpinan
        if ($note->user_id !== $user->id &&
            ! $user->hasRole('super_admin') &&
            ! $user->hasRole('tim_monev') &&
            ! $user->hasRole('sekre_pimpinan')) {
            abort(403);
        }

        $note->delete();

        return back()->with('success', 'Catatan berhasil dihapus.');
    }
}
