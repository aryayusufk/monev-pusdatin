<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Requests\SelectQuarterlyRequest;
use App\Models\Activity;
use App\Models\Bidang;
use App\Models\Iku;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $user = $request->user();

        $q = Activity::with(['bidang','ikus'])
            ->when($request->search, function ($query, $s) {
                $query->where(function ($w) use ($s) {
                    $w->where('kegiatan', 'ilike', "%{$s}%")
                      ->orWhere('tindak_lanjut', 'ilike', "%{$s}%")
                      ->orWhere('kendala', 'ilike', "%{$s}%")
                      ->orWhere('percepatan', 'ilike', "%{$s}%");
                });
            })
            ->when($request->year, fn($x, $y) => $x->ofYear((int)$y))
            ->when($request->month, fn($x, $m) => $x->ofMonth((int)$m))
            ->when($request->quarter, fn($x, $qtr) => $x->ofQuarter((int)$qtr))
            ->when($request->iso_week, fn($x, $w) => $x->ofWeek((int)$w))
            ->when($request->iku_id, fn($x, $i) => $x->byIku((int)$i))
            ->when($request->filled('selected_quarterly'), function ($x) use ($request) {
                return $x->selectedQuarterly((bool)$request->selected_quarterly);
            });

        // user bidang & monev bidang hanya boleh lihat bidang sendiri
        if ($user->hasRole('monev_bidang') || $user->hasRole('user_bidang')) {
            $q->where('bidang_id', $user->bidang_id);
        }

        $activities = $q->orderBy('occurred_at', 'desc')->paginate(20)->withQueryString();

        $ikus    = Iku::orderBy('kode')->get();
        $bidangs = Bidang::orderBy('nama')->get();

        return view('activities.index', [
            'activities' => $activities,
            'filters' => $request->only(['search','year','month','quarter','iso_week','iku_id','selected_quarterly']),
            'ikus' => $ikus,
            'bidangs' => $bidangs,
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Activity::class);

        $user = $request->user();

        $ikusQuery = Iku::active()->orderBy('kode');

        // kalau user bidang, batasi IKU yang terkait bidang dia
        if ($user->bidang_id) {
            $ikusQuery->whereHas('bidangs', fn($q) => $q->where('bidang_id', $user->bidang_id));
        }

        $ikus    = $ikusQuery->get();
        $bidangs = Bidang::orderBy('nama')->get();

        return view('activities.create', compact('ikus','bidangs','user'));
    }

    public function store(StoreActivityRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        // Jika user bidang / monev bidang, bidang_id dipaksa ke bidang user
        if ($user->hasRole('user_bidang') || $user->hasRole('monev_bidang')) {
            $data['bidang_id'] = $user->bidang_id;
        }

        // Set siapa yang membuat activity
        $data['created_by'] = $user->id;

        // Kalau kamu pakai status, boleh set default di sini
        if (! isset($data['status'])) {
            $data['status'] = 'submitted'; // atau 'draft' sesuai desainmu
        }

        // Buat record activity
        $activity = Activity::create($data);

        // Sinkronisasi relasi IKU (kalau ada di form)
        if (! empty($data['iku_ids'])) {
            $syncData = [];
            foreach ($data['iku_ids'] as $ikuId) {
                $syncData[$ikuId] = [
                    'contribution' => $data['contribution'][$ikuId] ?? null,
                ];
            }
            $activity->ikus()->sync($syncData);
        }

        return redirect()
            ->route('activities.show', $activity)
            ->with('success', 'Laporan kegiatan berhasil dibuat.');
    }

    public function show(Activity $activity)
    {
        $this->authorize('view', $activity);

        $activity->load(['bidang','ikus','files']);

        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity, Request $request)
    {
        $this->authorize('update', $activity);

        $user = $request->user();

        $ikusQuery = Iku::active()->orderBy('kode');
        if ($user->bidang_id) {
            $ikusQuery->whereHas('bidangs', fn($q) => $q->where('bidang_id', $user->bidang_id));
        }

        $ikus    = $ikusQuery->get();
        $bidangs = Bidang::orderBy('nama')->get();

        $activity->load('ikus');

        return view('activities.edit', compact('activity','ikus','bidangs','user'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $user = $request->user();
        $this->authorize('update', $activity);

        $data = $request->validated();

        // User bidang / monev bidang tidak boleh ganti bidang_id
        if ($user->hasRole('user_bidang') || $user->hasRole('monev_bidang')) {
            unset($data['bidang_id']);
        }

        // Tandai siapa yang mengupdate
        $data['updated_by'] = $user->id;

        $activity->update($data);

        if (! empty($data['iku_ids'])) {
            $syncData = [];
            foreach ($data['iku_ids'] as $ikuId) {
                $syncData[$ikuId] = [
                    'contribution' => $data['contribution'][$ikuId] ?? null,
                ];
            }
            $activity->ikus()->sync($syncData);
        }

        return redirect()
            ->route('activities.show', $activity)
            ->with('success', 'Laporan kegiatan berhasil diperbarui.');
    }


    public function destroy(Activity $activity)
    {
        $this->authorize('delete', $activity);

        $activity->delete();

        return redirect()
            ->route('activities.index')
            ->with('success', 'Laporan kegiatan berhasil dihapus.');
    }

    public function selectQuarterly(SelectQuarterlyRequest $request, Activity $activity)
    {
        $data = $request->validated();

        $activity->is_selected_quarterly = (bool) $data['select'];
        $activity->selected_quarterly_scope = $activity->is_selected_quarterly
            ? $data['selected_quarterly_scope']
            : null;
        $activity->selected_quarterly_by = $request->user()->id;
        $activity->selected_quarterly_at = now();
        $activity->save();

        return redirect()
            ->back()
            ->with('success', 'Status seleksi triwulan diperbarui.');
    }
}
