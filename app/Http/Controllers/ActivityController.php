<?php

namespace App\Http\Controllers;

use App\Http\Requests\SelectQuarterlyRequest;
use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Bidang;
use App\Models\Iku;
use App\Models\SystemLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $user = $request->user();

        $q = Activity::with(['bidang', 'ikus', 'files'])
            ->when($request->filled('start') && $request->filled('end'), function (Builder $x) use ($request) {
                $x->whereBetween('occurred_at', [
                    Carbon::parse($request->start)->startOfDay(),
                    Carbon::parse($request->end)->endOfDay(),
                ]);
            })
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $s = $request->search;
                $query->where(function (Builder $w) use ($s) {
                    $w->where('kegiatan', 'ilike', "%{$s}%")
                        ->orWhere('tindak_lanjut', 'ilike', "%{$s}%")
                        ->orWhere('kendala', 'ilike', "%{$s}%")
                        ->orWhere('percepatan', 'ilike', "%{$s}%");
                });
            })
            ->when($request->year, fn(Builder $x, $y) => $x->ofYear((int) $y))
            ->when($request->month, fn(Builder $x, $m) => $x->ofMonth((int) $m))
            ->when($request->quarter, fn(Builder $x, $qtr) => $x->ofQuarter((int) $qtr))
            ->when($request->iso_week, fn(Builder $x, $w) => $x->ofWeek((int) $w))
            ->when($request->iku_id, fn(Builder $x, $i) => $x->byIku((int) $i))
            ->when($request->filled('selected_quarterly'), function (Builder $x) use ($request) {
                return $x->selectedQuarterly((bool) $request->selected_quarterly);
            });

        // Filter bidang berdasarkan role / dropdown
        $this->applyBidangFilter($q, $request);

        $activities = $q->orderBy('occurred_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $columns = [
            'Kegiatan',
            'Tindak Lanjut',
            'Kendala',
            'Percepatan',
            'Data Pendukung & Nota Dinas',
        ];

        $rows = $activities->getCollection()->map(function (Activity $act) {
            $file = $act->files?->first();

            $dataPendukung = '-';
            if ($file) {
                $url = route('files.download', $file->id);
                $dataPendukung =
                    '<a href="' . $url . '" class="text-acc-base-default hover:underline">' .
                    e($file->original_name) .
                    '</a>';
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

        $ikus = $this->ikusQueryForRequest($request)->get();
        $bidangs = Bidang::orderBy('nama')->get();

        $bidangName = null;
        if ($request->filled('bidang_id')) {
            $bidangName = Bidang::find((int) $request->bidang_id)?->nama;
        } elseif ($user?->bidang_id) {
            $bidangName = Bidang::find((int) $user->bidang_id)?->nama;
        }

        $pageTitle = $request->filled('title')
            ? trim($request->title . ($bidangName ? " {$bidangName}" : ''))
            : 'Laporan Kegiatan';

        return view('activities.index', [
            'activities' => $activities,
            'columns' => $columns,
            'rows' => $rows,
            'filters' => $request->only(['search', 'year', 'month', 'quarter', 'iso_week', 'iku_id', 'selected_quarterly', 'bidang_id']),
            'ikus' => $ikus,
            'bidangs' => $bidangs,
            'pageTitle' => $pageTitle,
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('create', Activity::class);

        $user = $request->user();

        $ikusQuery = Iku::active()->orderBy('kode');

        if ($user->bidang_id) {
            $ikusQuery->whereHas('bidangs', fn(Builder $q) => $q->where('bidangs.id', $user->bidang_id));
        }

        $ikus = $ikusQuery->get();
        $bidangs = Bidang::orderBy('nama')->get();

        return view('activities.create', compact('ikus', 'bidangs', 'user'));
    }

    public function store(StoreActivityRequest $request)
    {
        $user = $request->user();
        $data = $request->validated();

        if ($user->hasRole('user_bidang') || $user->hasRole('monev_bidang')) {
            $data['bidang_id'] = $user->bidang_id;
        }

        $data['created_by'] = $user->id;
        $data['status'] = $data['status'] ?? 'submitted';

        $activity = Activity::create($data);

        $this->storeOptionalPdf($activity, $request);
        $this->syncIkus($activity, $data);

        $bidangName = $user->bidang?->nama ?? '-';
        $roleStr = $user->roles->pluck('name')->join(', ');

        SystemLog::write(
            'activity.created',
            "{$user->name} ({$roleStr} - {$bidangName}) membuat laporan kegiatan: " . str($activity->kegiatan)->limit(80)
        );

        return redirect()
            ->route('activities.index')
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
            $ikusQuery->whereHas('bidangs', fn(Builder $q) => $q->where('bidang.id', $user->bidang_id));
        }

        $ikus = $ikusQuery->get();
        $bidangs = Bidang::orderBy('nama')->get();

        $activity->load('ikus');

        return view('activities.edit', compact('activity', 'ikus', 'bidangs', 'user'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity)
    {
        $this->authorize('update', $activity);

        $user = $request->user();
        $data = $request->validated();

        if ($user->hasRole('user_bidang') || $user->hasRole('monev_bidang')) {
            unset($data['bidang_id']);
        }

        $data['updated_by'] = $user->id;

        $activity->update($data);

        $this->storeOptionalPdf($activity, $request);
        $this->syncIkus($activity, $data);

        $bidangName = $user->bidang?->nama ?? '-';
        $roleStr = $user->roles->pluck('name')->join(', ');

        SystemLog::write(
            'activity.updated',
            "{$user->name} ({$roleStr} - {$bidangName}) memperbarui laporan kegiatan (ID: {$activity->id})"
        );

        return redirect()
            ->route('activities.index')
            ->with('success', 'Laporan kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity, Request $request)
    {
        $this->authorize('delete', $activity);

        $user = $request->user();
        $roleStr = $user->roles->pluck('name')->join(', ');
        $bidangName = $activity->bidang?->nama ?? '-';

        $id = $activity->id;
        $activity->delete();

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

        $activity->is_selected_quarterly = (bool) $data['select'];
        $activity->selected_quarterly_scope = $activity->is_selected_quarterly
            ? $data['selected_quarterly_scope']
            : null;
        $activity->selected_quarterly_by = $request->user()->id;
        $activity->selected_quarterly_at = now();
        $activity->save();

        $actor = $request->user();
        $roleStr = $actor->roles->pluck('name')->join(', ');
        $statusText = $activity->is_selected_quarterly ? 'memilih' : 'membatalkan pilihan';
        $scope = $activity->selected_quarterly_scope ?? '-';

        SystemLog::write(
            'activity.quarterly_selection',
            "{$actor->name} ({$roleStr}) {$statusText} laporan kegiatan (ID: {$activity->id}) untuk laporan triwulan (scope: {$scope})"
        );

        return back()->with('success', 'Status seleksi triwulan diperbarui.');
    }

    /**
     * Halaman daftar card laporan (mingguan/bulanan berdasarkan period)
     */
    public function weeklyIndex(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        // Tahun dropdown: tahun yang ada data + 1 tahun setelah max
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

        $year = (int) $request->input('year', $years->last());
        $period = $request->input('period'); // 'month' atau 'quarter'
        $value = $request->input('value');   // month: 1-12, quarter: 1-4

        $ikus = $this->ikusQueryForRequest($request)->get();
        $bidangs = Bidang::orderBy('nama')->get();

        $cards = [];
        if (!empty($period) && !empty($value)) {
            if ($period === 'month') {
                $cards = $this->buildWeekCardsForMonth($year, (int) $value);
            } elseif ($period === 'quarter') {
                $cards = $this->buildMonthCardsForQuarter($year, (int) $value);
            }
        }

        return view('reports.bidang.index', [
            'year' => $year,
            'years' => $years,
            'period' => $period,
            'value' => $value,
            'cards' => $cards,
            'ikus' => $ikus,
            'bidangs' => $bidangs,
        ]);
    }


    public function weeklyReport(Request $request, int $year, int $week)
    {
        Gate::authorize('viewAny', Activity::class);

        $query = Activity::with('files')
            ->where('year', $year)
            ->where('iso_week', $week);

        $this->applyBidangFilter($query, $request);
        $this->applyIkuFilter($query, $request);

        $rows = $query->orderBy('occurred_at')->get()->map(function (Activity $act) {
            $file = $act->files->first();

            return [
                'id' => $act->id,
                'kegiatan' => $act->kegiatan,
                'tindak_lanjut' => $act->tindak_lanjut ?? '-',
                'kendala' => $act->kendala ?? '-',
                'percepatan' => $act->percepatan ?? '-',
                'file_name' => $file?->original_name ?? '-',
                'file_url' => $file ? route('files.download', $file->id) : null,
            ];
        });

        return view('reports.bidang.weekly-report', [
            'year' => $year,
            'week' => $week,
            'rows' => $rows,
        ]);
    }

    public function monthlyReport(Request $request, int $year, int $month)
    {
        Gate::authorize('viewAny', Activity::class);

        $query = Activity::with('files')
            ->where('year', $year)
            ->where('month', $month);

        $this->applyBidangFilter($query, $request);
        $this->applyIkuFilter($query, $request);

        $rows = $query->orderBy('occurred_at')->get()->map(function (Activity $act) {
            $file = $act->files->first();

            return [
                'id' => $act->id,
                'kegiatan' => $act->kegiatan,
                'tindak_lanjut' => $act->tindak_lanjut ?? '-',
                'kendala' => $act->kendala ?? '-',
                'percepatan' => $act->percepatan ?? '-',
                'file_name' => $file?->original_name ?? '-',
                'file_url' => $file ? route('files.download', $file->id) : null,
            ];
        });

        $dt = Carbon::createFromDate($year, $month, 1);

        return view('reports.bidang.monthly-report', [
            'year' => $year,
            'month' => $month,
            'month_label' => $dt->translatedFormat('F'),
            'rows' => $rows,
        ]);
    }

    public function weeklyExportRange(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        if (!$request->filled('start') || !$request->filled('end')) {
            abort(422, 'Parameter start dan end wajib diisi.');
        }

        $start = Carbon::parse($request->start)->startOfDay();
        $end = Carbon::parse($request->end)->endOfDay();

        $query = Activity::with(['bidang', 'files', 'ikus'])
            ->whereBetween('occurred_at', [$start, $end]);

        $this->applyBidangFilter($query, $request);
        $this->applyIkuFilter($query, $request);

        $activities = $query->orderBy('occurred_at')->get();

        $periode = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
        $title = $request->filled('title') ? mb_substr($request->title, 0, 80) : 'Laporan Mingguan';

        $pdf = Pdf::loadView('reports.bidang.weekly-pdf', [
            'title'   => $title,
            'periode' => $periode,
            'groups'  => $this->groupActivitiesByBidang($activities),
        ])->setPaper('a4', 'landscape');

        $safeTitle = preg_replace('/[^A-Za-z0-9\-_]/', '-', $title);
        $filename = "{$safeTitle}-{$start->toDateString()}-{$end->toDateString()}.pdf";

        return $pdf->download($filename);
    }

    public function monthlyExportRange(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $month = (int) $request->month;
        $year  = (int) $request->year;

        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end   = $start->copy()->endOfMonth()->endOfDay();

        $query = Activity::with(['bidang', 'files', 'ikus'])
            ->whereBetween('occurred_at', [$start, $end]);

        $this->applyBidangFilter($query, $request);
        $this->applyIkuFilter($query, $request);

        $activities = $query->orderBy('occurred_at')->get();

        $periode = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
        $title = $request->filled('title') ? mb_substr($request->title, 0, 80) : 'Laporan Bulanan';

        return Pdf::loadView('reports.bidang.monthly-pdf', [
            'title'     => $title,
            'periode'   => $periode,
            'year'      => $year,
            'monthName' => $start->translatedFormat('F'),
            'groups'    => $this->groupActivitiesByBidang($activities),
        ])->setPaper('a4', 'landscape')
            ->download("Laporan-Bulanan-{$year}-{$month}.pdf");
    }

    public function quarterlyExportRange(Request $request)
    {
        Gate::authorize('viewAny', Activity::class);

        $quarter = (int) $request->quarter;
        $year = (int) $request->year;

        $startMonth = (($quarter - 1) * 3) + 1;
        $start = Carbon::create($year, $startMonth, 1)->startOfDay();
        $end = $start->copy()->addMonths(2)->endOfMonth()->endOfDay();

        $query = Activity::with(['bidang', 'files', 'ikus'])
            ->whereBetween('occurred_at', [$start, $end]);

        $this->applyBidangFilter($query, $request);
        $this->applyIkuFilter($query, $request);

        $activities = $query->orderBy('occurred_at')->get();

        $periode = $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y');
        $title = $request->filled('title') ? mb_substr($request->title, 0, 80) : 'Laporan Triwulan';

        return Pdf::loadView('reports.bidang.quarterly-pdf', [
            'title'   => $title,
            'periode' => $periode,
            'year'    => $year,
            'quarter' => $quarter,
            'groups'  => $this->groupActivitiesByBidang($activities),
        ])->setPaper('a4', 'landscape')
            ->download("Laporan-Triwulan-{$year}-Q{$quarter}.pdf");
    }

    private function ikusQueryForRequest(Request $request): Builder
    {
        $q = Iku::query()->orderBy('kode');

        // default: hanya IKU aktif
        $q->where('is_active', true);

        $bidangId = $this->resolveBidangId($request);
        if ($bidangId) {
            // gunakan scope di model IKU (lebih konsisten dan menghindari kolom ambigu)
            $q->forBidang($bidangId);
        }

        return $q;
    }


    /* =========================
     * Helpers
     * ========================= */

    private function storeOptionalPdf(Activity $activity, Request $request): void
    {
        if (!$request->hasFile('file_upload')) {
            return;
        }

        $file = $request->file('file_upload');

        if (!$file || !$file->isValid()) {
            return;
        }

        $path = $file->store("activities/{$activity->id}", 'public');

        $activity->files()->create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    private function syncIkus(Activity $activity, array $data): void
    {
        if (empty($data['iku_ids'])) {
            return;
        }

        $syncData = [];
        foreach ($data['iku_ids'] as $ikuId) {
            $syncData[$ikuId] = [
                'contribution' => $data['contribution'][$ikuId] ?? null,
            ];
        }

        $activity->ikus()->sync($syncData);
    }

    private function resolveBidangId(Request $request): ?int
    {
        $user = $request->user();

        if ($user->hasRole('monev_bidang') || $user->hasRole('user_bidang')) {
            return (int) $user->bidang_id;
        }

        if ($request->filled('bidang_id')) {
            return (int) $request->bidang_id;
        }

        return null;
    }

    private function applyBidangFilter(Builder $query, Request $request): void
    {
        $bidangId = $this->resolveBidangId($request);
        if ($bidangId) {
            $query->where('bidang_id', $bidangId);
        }
    }

    private function applyIkuFilter(Builder $query, Request $request): void
    {
        if (!$request->filled('iku_id')) {
            return;
        }

        $ikuId = (int) $request->iku_id;
        $query->whereHas('ikus', fn(Builder $q) => $q->where('ikus.id', $ikuId));
    }

    private function buildWeekCardsForMonth(int $year, int $month): array
    {
        $start = Carbon::create($year, $month, 1)->startOfDay();
        $end = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $cards = [];
        $cursor = $start->copy();
        $i = 1;

        while ($cursor->lte($end)) {
            $weekStart = $cursor->copy();
            $weekEnd = $cursor->copy()->addDays(6)->endOfDay();

            if ($weekEnd->gt($end)) {
                $weekEnd = $end->copy();
            }

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
            $end = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

            $cards[] = [
                'title' => 'Laporan Bulan ' . $start->translatedFormat('F'),
                'subtitle' => $start->translatedFormat('d M Y') . ' - ' . $end->translatedFormat('d M Y'),
                'start' => $start->toDateString(),
                'end' => $end->toDateString(),
            ];
        }

        return $cards;
    }

    private function groupActivitiesByBidang($activities)
    {
        return $activities
            ->groupBy(fn(Activity $a) => $a->bidang?->nama ?? 'TANPA BIDANG')
            ->map(fn($items) => $items->values());
    }
}
