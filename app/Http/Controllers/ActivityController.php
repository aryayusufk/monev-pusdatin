<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Http\Requests\SelectQuarterlyRequest;
use App\Models\Activity;
use App\Models\Bidang;
use App\Models\Iku;
use App\Models\SystemLog;
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
            'filters'    => $request->only(['search','year','month','quarter','iso_week','iku_id','selected_quarterly']),
            'ikus'       => $ikus,
            'bidangs'    => $bidangs,
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

        // ===== LOG ACTIVITY CREATED =====
        $bidangName = $user->bidang?->nama ?? '-';
        $roleStr    = $user->roles->pluck('name')->join(', ');

        SystemLog::write(
            'activity.created',
            "{$user->name} ({$roleStr} - {$bidangName}) membuat laporan kegiatan: " .
            str($activity->kegiatan)->limit(80)
        );

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

        // ===== LOG ACTIVITY UPDATED =====
        $bidangName = $user->bidang?->nama ?? '-';
        $roleStr    = $user->roles->pluck('name')->join(', ');

        SystemLog::write(
            'activity.updated',
            "{$user->name} ({$roleStr} - {$bidangName}) memperbarui laporan kegiatan (ID: {$activity->id})"
        );

        return redirect()
            ->route('activities.show', $activity)
            ->with('success', 'Laporan kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity, Request $request)
    {
        $this->authorize('delete', $activity);

        $user       = $request->user();
        $roleStr    = $user->roles->pluck('name')->join(', ');
        $bidangName = $activity->bidang?->nama ?? '-';

        $id = $activity->id;

        $activity->delete();

        // ===== LOG ACTIVITY DELETED =====
        SystemLog::write(
            'activity.deleted',
            "{$user->name} ({$roleStr} - {$bidangName}) menghapus laporan kegiatan (ID: {$id})"
        );

        return redirect()
            ->route('activities.index')
            ->with('success', 'Laporan kegiatan berhasil dihapus.');
    }

    public function selectQuarterly(SelectQuarterlyRequest $request, Activity $activity)
    {
        $data = $request->validated();

        $activity->is_selected_quarterly  = (bool) $data['select'];
        $activity->selected_quarterly_scope = $activity->is_selected_quarterly
            ? $data['selected_quarterly_scope']
            : null;
        $activity->selected_quarterly_by = $request->user()->id;
        $activity->selected_quarterly_at = now();
        $activity->save();

        $isSelected = $activity->is_selected_quarterly;
        $scope      = $activity->selected_quarterly_scope ?? '-';
        $actor      = $request->user();
        $roleStr    = $actor->roles->pluck('name')->join(', ');
        $statusText = $isSelected ? "memilih" : "membatalkan pilihan";

        SystemLog::write(
            'activity.quarterly_selection',
            "{$actor->name} ({$roleStr}) {$statusText} laporan kegiatan (ID: {$activity->id}) untuk laporan triwulan (scope: {$scope})"
        );

        return redirect()
            ->back()
            ->with('success', 'Status seleksi triwulan diperbarui.');
    }

    /**
     * Laporan Mingguan per Bidang - daftar minggu
     * URL contoh: /laporan-bidang/mingguan?year=2025&month=1
     */
    public function weeklyIndex(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $user  = $request->user();
        $year  = (int) ($request->input('year') ?: now()->year);
        $month = $request->input('month'); // bisa null (semua bulan di tahun tsb)

        $query = Activity::query()
            ->where('year', $year);

        // Filter bulan (kalau UI pilih bulan)
        if (! empty($month)) {
            $query->where('month', (int) $month);
        }

        // Batasi per bidang sesuai role
        if ($user->hasRole('monev_bidang') || $user->hasRole('user_bidang')) {
            $query->where('bidang_id', $user->bidang_id);
        } else {
            if ($request->filled('bidang_id')) {
                $query->where('bidang_id', (int) $request->bidang_id);
            }
        }

        // Filter IKU kalau dikirim dari UI
        if ($request->filled('iku_id')) {
            $ikuId = (int) $request->iku_id;
            $query->whereHas('ikus', fn ($q) => $q->where('ikus.id', $ikuId));
        }

        // Ambil daftar minggu (iso_week) yang punya data
        $weeks = $query
            ->select('iso_week')
            ->distinct()
            ->orderBy('iso_week')
            ->get()
            ->map(function ($row) use ($year) {
                return [
                    'year'  => $year,
                    'week'  => $row->iso_week,
                    'label' => 'Laporan Minggu Ke - ' . $row->iso_week,
                ];
            });

        $ikus    = Iku::orderBy('kode')->get();
        $bidangs = Bidang::orderBy('nama')->get();

        return view('reports.bidang.weekly-index', [
            'year'    => $year,
            'month'   => $month,
            'weeks'   => $weeks,
            'ikus'    => $ikus,
            'bidangs' => $bidangs,
        ]);
    }

    /**
     * Laporan Mingguan per Bidang - isi 1 minggu tertentu
     * URL contoh: /laporan-bidang/mingguan/2025/1
     */
    public function weeklyShow(Request $request, int $year, int $week)
    {
        Gate::authorize('viewAny', Activity::class);

        $user = $request->user();

        $query = Activity::with('files')
            ->where('year', $year)
            ->where('iso_week', $week);

        // Batasi per bidang sesuai role
        if ($user->hasRole('monev_bidang') || $user->hasRole('user_bidang')) {
            $query->where('bidang_id', $user->bidang_id);
        } else {
            if ($request->filled('bidang_id')) {
                $query->where('bidang_id', (int) $request->bidang_id);
            }
        }

        // Filter IKU kalau ada
        if ($request->filled('iku_id')) {
            $ikuId = (int) $request->iku_id;
            $query->whereHas('ikus', fn ($q) => $q->where('ikus.id', $ikuId));
        }

        $activities = $query
            ->orderBy('occurred_at')
            ->get();

        $rows = $activities->map(function (Activity $act) {
            $file = $act->files->first(); // asumsi 1 file utama per laporan

            return [
                'id'            => $act->id,
                'kegiatan'      => $act->kegiatan,
                'tindak_lanjut' => $act->tindak_lanjut ?? '-',
                'kendala'       => $act->kendala ?? '-',
                'percepatan'    => $act->percepatan ?? '-',
                'file_name'     => $file?->original_name ?? '-',
                'file_url'      => $file ? route('files.download', $file->id) : null,
            ];
        });

        return view('reports.bidang.weekly-show', [
            'year' => $year,
            'week' => $week,
            'rows' => $rows,
        ]);
    }
}
