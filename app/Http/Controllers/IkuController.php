<?php

namespace App\Http\Controllers;

use App\Models\Iku;
use App\Models\Bidang;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class IkuController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', Iku::class);

        $ikus = Iku::with('bidangs')
            ->when($request->filled('tahun'), fn ($q) => $q->where('tahun', (int) $request->tahun))
            ->orderBy('kode')
            ->paginate(20);

        $columns = [
            'Nama IKU',
            'Tahun',
            'Deskripsi',
            'Bidang Terkait',
        ];

        $rows = collect($ikus->items())->map(function (Iku $iku) {
            return [
                // id dipakai untuk checkbox / action di komponen tabel
                'id'         => $iku->id,
                'nama'       => $iku->nama,
                'tahun'     => $iku->tahun ?? '-',
                'deskripsi'  => $iku->deskripsi ?? '-',
                'bidangs'    => $iku->bidangs->pluck('nama')->join(', ') ?: '-',
            ];
        });

        return view('ikus.index', [
            'ikus'    => $ikus,
            'columns' => $columns,
            'rows'    => $rows,
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();
        $this->authorize('create', Iku::class);

        $bidangs = Bidang::orderBy('nama')->get();

        return view('ikus.create', [
            'bidangs' => $bidangs,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Iku::class);

        $data = $request->validate([
            // TIDAK lagi minta 'kode' dari form
            'nama'          => ['required', 'string', 'max:255'],
            'deskripsi'     => ['nullable', 'string'],
            'tahun'        => ['required', 'integer', 'min:2000', 'max:2100'],
            'is_active'     => ['nullable', 'boolean'],
            'bidang_ids'    => ['nullable', 'array'],
            'bidang_ids.*'  => ['integer', 'exists:bidangs,id'],
        ]);

        $data['is_active'] = $data['is_active'] ?? false;

        // === Generate KODE IKU otomatis ===
        // contoh: IKU-001, IKU-002, dst
        $lastId = Iku::max('id') ?? 0;
        $nextNumber = $lastId + 1;
        $kodeAuto = 'IKU-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $iku = Iku::create([
            'kode'      => $kodeAuto,
            'nama'      => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tahun'    => $data['tahun'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        $iku->bidangs()->sync($data['bidang_ids'] ?? []);

        // ===== LOG IKU CREATED =====
        $actor   = $request->user();
        $roleStr = $actor->roles->pluck('name')->join(', ');

        SystemLog::write(
            'iku.created',
            "{$actor->name} ({$roleStr}) membuat IKU baru: [{$iku->kode}] {$iku->nama}"
        );

        return redirect()
            ->route('ikus.index')
            ->with('success', 'IKU berhasil dibuat.');
    }


    public function edit(Request $request, Iku $iku)
    {
        $user = $request->user();
        $this->authorize('update', $iku);

        $bidangs = Bidang::orderBy('nama')->get();
        $selectedBidangIds = $iku->bidangs()->pluck('bidangs.id')->toArray();

        return view('ikus.edit', [
            'iku'               => $iku,
            'bidangs'           => $bidangs,
            'selectedBidangIds' => $selectedBidangIds,
        ]);
    }

    public function update(Request $request, Iku $iku)
    {
        $this->authorize('update', $iku);

        $data = $request->validate([
            // 'kode' tidak diminta dari form
            'nama'          => ['required', 'string', 'max:255'],
            'deskripsi'     => ['nullable', 'string'],
            'tahun'        => ['required', 'integer', 'min:2000', 'max:2100'],
            'is_active'     => ['nullable', 'boolean'],
            'bidang_ids'    => ['nullable', 'array'],
            'bidang_ids.*'  => ['integer', 'exists:bidangs,id'],
        ]);

        $data['is_active'] = $data['is_active'] ?? false;

        $iku->update([
            // kode tetap pakai yang lama
            'nama'      => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tahun'    => $data['tahun'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        $iku->bidangs()->sync($data['bidang_ids'] ?? []);

                // ===== LOG IKU UPDATED =====
        $actor   = $request->user();
        $roleStr = $actor->roles->pluck('name')->join(', ');

        SystemLog::write(
            'iku.updated',
            "{$actor->name} ({$roleStr}) memperbarui IKU: [{$iku->kode}] {$iku->nama}"
        );

        return redirect()
            ->route('ikus.index')
            ->with('success', 'IKU berhasil diperbarui.');
    }

    public function destroy(Request $request, Iku $iku)
    {
        $this->authorize('delete', $iku);

        $actor   = $request->user();
        $roleStr = $actor->roles->pluck('name')->join(', ');

        $kode = $iku->kode;
        $nama = $iku->nama;

        $iku->delete(); // soft delete

        SystemLog::write(
            'iku.deleted',
            "{$actor->name} ({$roleStr}) menghapus IKU: [{$kode}] {$nama}"
        );

        return redirect()
            ->route('ikus.index')
            ->with('success', 'IKU berhasil dihapus.');
    }



    public function toggleActive(Request $request, Iku $iku)
    {
        $user = $request->user();
        $this->authorize('update', $iku);

        $iku->is_active = ! $iku->is_active;
        $iku->save();

             // ===== LOG IKU TOGGLE ACTIVE =====
        $status  = $iku->is_active ? 'aktif' : 'non-aktif';
        $actor   = $request->user();
        $roleStr = $actor->roles->pluck('name')->join(', ');

        SystemLog::write(
            'iku.toggled',
            "{$actor->name} ({$roleStr}) mengubah status IKU [{$iku->kode}] {$iku->nama} menjadi {$status}"
        );

        return redirect()
            ->route('ikus.index')
            ->with('success', 'Status aktif IKU berhasil diubah.');
    }
}
