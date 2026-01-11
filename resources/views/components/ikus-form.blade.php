@props([
    'title' => 'Form Title',
    'buttonLabel' => 'Button Label',
    'bidangs' => [],
    'iku' => null,
    'selectedBidangIds' => [],
])

@php
    $isEdit = isset($iku) && $iku;

    // selectedBidangIds default dari controller (edit)
    // old('bidang_ids') untuk kasus validasi gagal dan balik lagi ke form
    $selectedBidangIds = collect(old('bidang_ids', $selectedBidangIds))
        ->filter(fn($v) => $v !== null && $v !== '')
        ->map(fn($v) => (int) $v)
        ->all();
@endphp

<div class="flex flex-col w-full p-6 gap-6 rounded-lg bg-bg-base-default border border-bor-base-default">
    <div class="flex flex-row w-full justify-between">
        <x-icon-button onclick="history.back()">
            <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
        </x-icon-button>
        <h1 class="text-2xl font-semibold text-acc-base-default">{{ $title }}</h1>
    </div>

    <form action="{{ $isEdit ? route('ikus.update', $iku) : route('ikus.store') }}" method="POST"
        enctype="multipart/form-data" class="flex flex-col w-full gap-6 text-sm font-normal text-txt-base-default">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="nama" :value="__('Judul IKU')" />
            <x-text-input id="nama" class="block w-full" type="text" name="nama"
                :value="old('nama', $iku->nama ?? '')" required autofocus autocomplete="nama" />
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="deskripsi" :value="__('Deskripsi')" />
            <x-textarea-input id="deskripsi" class="block w-full h-20" name="deskripsi" required>
                {{ old('deskripsi', $iku->deskripsi ?? '') }}
            </x-textarea-input>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="tahun" :value="__('Tahun')" />
            <x-text-input id="tahun" class="block w-full" type="number" name="tahun"
                :value="old('tahun', $iku->tahun ?? '')" required autofocus autocomplete="tahun" />
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="bidang_id" :value="__('Bidang')" />

            <x-select-input id="bidang_id" name="bidang_ids[]" class="block w-full" multiple>
                <option value="" disabled @selected(empty($selectedBidangIds))>Pilih Bidang</option>

                @foreach ($bidangs as $bidang)
                    <option value="{{ $bidang->id }}"
                        @selected(in_array((int) $bidang->id, $selectedBidangIds, true))>
                        {{ $bidang->nama }}
                    </option>
                @endforeach
            </x-select-input>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="is_active" :value="__('Status Aktivasi')" />
            <input type="checkbox" id="is_active" name="is_active" value="1"
                @if (old('is_active', $iku->is_active ?? 1)) checked @endif
                class="w-4 h-4 rounded border-bor-base-default text-acc-base-default focus:ring-acc-base-default">
        </div>

        <div class="flex justify-end">
            <x-primary-button>
                {{ __($buttonLabel) }}
            </x-primary-button>
        </div>
    </form>
</div>
