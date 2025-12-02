<?php

namespace App\Http\Controllers;

use App\Models\Iku;
use App\Models\Bidang;
use Illuminate\Http\Request;

class IkuController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $this->authorize('viewAny', Iku::class);

        $ikus = Iku::with('bidangs')
            ->orderBy('kode')
            ->paginate(20);

        $columns = [
            'Nama IKU',
            'Target',
            'Deskripsi',
            'Bidang Terkait',
        ];

        $rows = collect($ikus->items())->map(function (Iku $iku) {
            return [
                // id dipakai untuk checkbox / action di komponen tabel
                'id'         => $iku->id,
                'nama'       => $iku->nama,
                'target'     => $iku->target ?? '-',
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
            'target'        => ['nullable', 'string', 'max:255'],
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
            'target'    => $data['target'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        $iku->bidangs()->sync($data['bidang_ids'] ?? []);

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
            'target'        => ['nullable', 'string', 'max:255'],
            'is_active'     => ['nullable', 'boolean'],
            'bidang_ids'    => ['nullable', 'array'],
            'bidang_ids.*'  => ['integer', 'exists:bidangs,id'],
        ]);

        $data['is_active'] = $data['is_active'] ?? false;

        $iku->update([
            // kode tetap pakai yang lama
            'nama'      => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'target'    => $data['target'] ?? null,
            'is_active' => $data['is_active'],
        ]);

        $iku->bidangs()->sync($data['bidang_ids'] ?? []);

        return redirect()
            ->route('ikus.index')
            ->with('success', 'IKU berhasil diperbarui.');
    }


    public function toggleActive(Request $request, Iku $iku)
    {
        $user = $request->user();
        $this->authorize('update', $iku);

        $iku->is_active = ! $iku->is_active;
        $iku->save();

        return redirect()
            ->route('ikus.index')
            ->with('success', 'Status aktif IKU berhasil diubah.');
    }
}
