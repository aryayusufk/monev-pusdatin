<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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

        $q = Activity::with(['bidang', 'ikus', 'files'])
            ->when($request->filled('start') && $request->filled('end'), function ($x) use ($request) {
                $x->whereBetween('occurred_at', [
                    Carbon::parse($request->start)->startOfDay(),
                    Carbon::parse($request->end)->endOfDay(),
                ]);
            })

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
        } else {
            if ($request->filled('bidang_id')) {
                $q->where('bidang_id', (int) $request->bidang_id);
            }
        }


        $activities = $q->orderBy('occurred_at', 'desc')->paginate(20)->withQueryString();

        $columns = [
            'Kegiatan',
            'Tindak Lanjut',
            'Kendala',
            'Percepatan',
            'Data Pendukung & Nota Dinas',
        ];

        $rows = $activities->getCollection()->map(function (Activity $act) {
            // kalau di index perlu file, pastikan relasi files di-load (lihat catatan di bawah)
            $file = $act->files?->first();

            $dataPendukung = '-';
            if ($file) {
                $url = route('files.download', $file->id);
                $dataPendukung = '<a href="' . $url . '" class="text-acc-base-default hover:underline">'
                    . e($file->original_name)
                    . '</a>';
            }

            return [
                'id' => $act->id,
                'kegiatan' => e($act->kegiatan),
                'tindak_lanjut' => e($act->tindak_lanjut ?? '-'),
                'kendala' => e($act->kendala ?? '-'),
                'percepatan' => e($act->percepatan ?? '-'),
                'data_pendukung' => $dataPendukung,
            ];
        })->values()->toArray();

        $ikus    = Iku::orderBy('kode')->get();
        $bidangs = Bidang::orderBy('nama')->get();

        $bidangName = null;

        if ($request->filled('bidang_id')) {
            $bidangName = \App\Models\Bidang::find($request->bidang_id)?->nama;
        } elseif ($request->user()?->bidang_id) {
            $bidangName = \App\Models\Bidang::find($request->user()->bidang_id)?->nama;
        }

        $titleFromReq = $request->filled('title') ? mb_substr($request->title, 0, 80) : null;

        $pageTitle = $request->filled('title')
            ? trim($request->title . ($bidangName ? " {$bidangName}" : ''))
            : 'Laporan Kegiatan';

        return view('activities.index', [
            'activities' => $activities,
            'columns'    => $columns,
            'rows'       => $rows,
            'filters'    => $request->only(['search', 'year', 'month', 'quarter', 'iso_week', 'iku_id', 'selected_quarterly']),
            'ikus'       => $ikus,
            'bidangs'    => $bidangs,
            'pageTitle' => $pageTitle,
        ]);
    }

    private function storeOptionalPdf(Activity $activity, Request $request): void
    {
        if (! $request->hasFile('file_upload')) {
            return;
        }

        $file = $request->file('file_upload');

        if (! $file || ! $file->isValid()) {
            return;
        }

        $path = $file->store("activities/{$activity->id}", 'public');

        $activity->files()->create([
            'path'          => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
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

        return view('activities.create', compact('ikus', 'bidangs', 'user'));
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
        $this->storeOptionalPdf($activity, $request);

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
            ->route('activities.index', $activity)
            ->with('success', 'Laporan kegiatan berhasil dibuat.');
    }

    public function show(Activity $activity)
    {
        $this->authorize('view', $activity);

        $activity->load(['bidang', 'ikus', 'files']);

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

        return view('activities.edit', compact('activity', 'ikus', 'bidangs', 'user'));
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
        $this->storeOptionalPdf($activity, $request);

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
            ->route('activities.index', $activity)
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

    private function resolveBidangId(Request $request): ?int
    {
        $user = $request->user();

        // User bidang / monev bidang wajib hanya bidangnya sendiri
        if ($user->hasRole('monev_bidang') || $user->hasRole('user_bidang')) {
            return (int) $user->bidang_id;
        }

        // Admin/super admin: boleh pilih via dropdown (query string)
        if ($request->filled('bidang_id')) {
            return (int) $request->bidang_id;
        }

        return null;
    }

    private function buildWeekCardsForMonth(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $cards = [];
        $cursor = $start->copy();
        $i = 1;

        while ($cursor->lte($end)) {
            $weekStart = $cursor->copy();
            $weekEnd   = $cursor->copy()->addDays(6)->endOfDay();
            if ($weekEnd->gt($end)) $weekEnd = $end->copy();

            $cards[] = [
                'title' => "Laporan Minggu Ke-{$i}",
                'subtitle' => $weekStart->translatedFormat('d M Y') . ' - ' . $weekEnd->translatedFormat('d M Y'),
                'start' => $weekStart->toDateString(),
                'end' => $weekEnd->toDateString(),
            ];

            $cursor = $cursor->addDays(7)->startOfDay();
            $i++;
        }

        return $cards;
    }

    private function buildMonthCardsForQuarter(int $year, int $quarter): array
    {
        $firstMonth = (($quarter - 1) * 3) + 1;

        $cards = [];
        for ($i = 0; $i < 3; $i++) {
            $month = $firstMonth + $i;
            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end   = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

            $cards[] = [
                'title' => 'Laporan Bulan ' . $start->translatedFormat('F'),
                'subtitle' => $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y'),
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ];
        }

        return $cards;
    }

    /**
     * Laporan Mingguan per Bidang - daftar minggu
     * URL contoh: /laporan-bidang/mingguan?year=2025&month=1
     */
    public function weeklyIndex(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $user   = $request->user();

        // Tahun dropdown: hanya tahun yang ada data + 1 tahun setelah tahun terbesar
        $yearsInData = Activity::query()
            ->select('year')
            ->whereNotNull('year')
            ->distinct()
            ->orderBy('year', 'asc')
            ->pluck('year');

        if ($yearsInData->isEmpty()) {
            $years = collect([now()->year, now()->year + 1]);
        } else {
            $maxYear = (int) $yearsInData->max();
            $years = $yearsInData->values();
            $years->push($maxYear + 1);
        }

        // default year = nilai request kalau ada, kalau tidak pakai tahun terakhir di dropdown
        $year   = (int) $request->input('year', $years->last());

        $period = $request->input('period'); // 'month' atau 'quarter'
        $value  = $request->input('value');  // jika month: 1-12, jika quarter: 1-4

        $ikus    = Iku::orderBy('kode')->get();
        $bidangs = Bidang::orderBy('nama')->get();

        $cards = [];

        if (!empty($period) && !empty($value)) {
            if ($period === 'month') {
                $cards = $this->buildWeekCardsForMonth($year, (int) $value);
            }

            if ($period === 'quarter') {
                $cards = $this->buildMonthCardsForQuarter($year, (int) $value);
            }
        }

        return view('reports.bidang.index', [
            'year'    => $year,
            'years'   => $years,
            'period'  => $period,
            'value'   => $value,
            'cards'   => $cards,
            'ikus'    => $ikus,
            'bidangs' => $bidangs,
        ]);


        // ============== JIKA FILTER BELUM LENGKAP ==============
        // Tahun selalu ada (default), tapi period & value WAJIB dipilih dulu.
        if (empty($period) || empty($value)) {
            return view('reports.bidang.index', [
                'year'    => $year,
                'period'  => $period,
                'value'   => $value,
                'mode'    => $mode,
                'weeks'   => $weeks,
                'months'  => $months,
                'ikus'    => $ikus,
                'bidangs' => $bidangs,
            ]);
        }
        // =======================================================

        // Base query
        $query = Activity::query()
            ->where('year', $year);

        // Batasi per bidang sesuai role
        $bidangId = $this->resolveBidangId($request);
        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }


        // Filter IKU
        if ($request->filled('iku_id')) {
            $ikuId = (int) $request->iku_id;
            $query->whereHas('ikus', fn($q) => $q->where('ikus.id', $ikuId));
        }

        // ============== PERIODE = BULAN (TAMPIL MINGGU) ==============
        if ($period === 'month') {
            $mode  = 'weekly';
            $month = (int) $value;

            $queryMonth = (clone $query)->where('month', $month);

            $weeks = $queryMonth
                ->select('iso_week')
                ->distinct()
                ->orderBy('iso_week')
                ->get()
                ->map(function ($row) use ($year) {
                    return [
                        'year' => $year,
                        'week' => $row->iso_week, // tetap iso_week untuk backend
                    ];
                });
        }

        // ============== PERIODE = TRIWULAN (TAMPIL BULAN) ==============
        if ($period === 'quarter') {
            $mode    = 'monthly';
            $quarter = (int) $value;

            // Q1: 1-3, Q2: 4-6, Q3: 7-9, Q4: 10-12
            $startMonth = ($quarter - 1) * 3 + 1;
            $endMonth   = $startMonth + 2;

            $queryQuarter = (clone $query)->whereBetween('month', [$startMonth, $endMonth]);

            $months = $queryQuarter
                ->select('month')
                ->distinct()
                ->orderBy('month')
                ->get()
                ->map(function ($row) use ($year) {
                    $m   = $row->month;
                    $dt  = Carbon::createFromDate($year, $m, 1);
                    return [
                        'year'  => $year,
                        'month' => $m,
                        'label' => $dt->translatedFormat('F'), // Nama bulan (Januari, dst)
                    ];
                });
        }

        return view('reports.bidang.index', [
            'year'    => $year,
            'period'  => $period,
            'value'   => $value,
            'mode'    => $mode,
            'weeks'   => $weeks,
            'months'  => $months,
            'ikus'    => $ikus,
            'bidangs' => $bidangs,
        ]);
    }


    /**
     * Laporan Mingguan per Bidang - isi 1 minggu tertentu
     * URL contoh: /laporan-bidang/mingguan/2025/1
     */
    public function weeklyReport(Request $request, int $year, int $week)
    {
        Gate::authorize('viewAny', Activity::class);

        $user = $request->user();

        $query = Activity::with('files')
            ->where('year', $year)
            ->where('iso_week', $week);

        // Batasi per bidang sesuai role
        $bidangId = $this->resolveBidangId($request);
        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }


        // Filter IKU kalau ada
        if ($request->filled('iku_id')) {
            $ikuId = (int) $request->iku_id;
            $query->whereHas('ikus', fn($q) => $q->where('ikus.id', $ikuId));
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

        return view('reports.bidang.weekly-report', [
            'year' => $year,
            'week' => $week,
            'rows' => $rows,
        ]);
    }

    public function weeklyExport(Request $request, int $year, int $week)
    {
        Gate::authorize('viewAny', Activity::class);

        $user = $request->user();

        // Query Activities untuk minggu tersebut
        $query = Activity::with(['bidang', 'files', 'ikus'])
            ->where('year', $year)
            ->where('iso_week', $week);

        // Filter bidang jika user_bidang atau monev_bidang
        $bidangId = $this->resolveBidangId($request);
        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }


        // Filter IKU jika ada
        if ($request->filled('iku_id')) {
            $ikuId = (int) $request->iku_id;
            $query->whereHas('ikus', fn($q) => $q->where('ikus.id', $ikuId));
        }

        $activities = $query->orderBy('occurred_at')->get();

        // Hitung teks periode untuk header PDF
        if ($activities->isNotEmpty()) {
            $start = Carbon::parse($activities->min('occurred_at'));
            $end   = Carbon::parse($activities->max('occurred_at'));

            if ($start->isSameMonth($end)) {
                $periode = $start->translatedFormat('d') . ' - ' . $end->translatedFormat('d F Y');
            } else {
                $periode = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
            }
        } else {
            $periode = '-';
        }

        // Generate PDF
        $pdf = Pdf::loadView('reports.bidang.weekly-pdf', [
            'year'        => $year,
            'week'        => $week,
            'periode'     => $periode,
            'activities'  => $activities,
        ])->setPaper('a4', 'landscape');

        $filename = "Laporan-Mingguan-{$year}-Minggu-{$week}.pdf";

        return $pdf->download($filename);
    }


    public function monthlyReport(Request $request, int $year, int $month)
    {
        Gate::authorize('viewAny', Activity::class);

        $user = $request->user();

        $query = Activity::with('files')
            ->where('year', $year)
            ->where('month', $month);

        // Batasi per bidang sesuai role
        $bidangId = $this->resolveBidangId($request);
        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }


        // Filter IKU kalau mau (opsional)
        if ($request->filled('iku_id')) {
            $ikuId = (int) $request->iku_id;
            $query->whereHas('ikus', fn($q) => $q->where('ikus.id', $ikuId));
        }

        $activities = $query
            ->orderBy('occurred_at')
            ->get();

        $rows = $activities->map(function (Activity $act) {
            $file = $act->files->first(); // asumsi 1 file utama

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

        $dt = Carbon::createFromDate($year, $month, 1);

        return view('reports.bidang.monthly-report', [
            'year'  => $year,
            'month' => $month,
            'month_label' => $dt->translatedFormat('F'),
            'rows'  => $rows,
        ]);
    }
}
