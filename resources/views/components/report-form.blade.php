@props([
    'title' => 'Form Title',
    'buttonLabel' => 'Button Label',
    'ikus' => [],
])

<div class="flex flex-col w-full p-6 gap-6 rounded-xl bg-bg-base-default border border-bor-base-default">
    <div class="w-full text-2xl font-semibold text-acc-base-default">
        <h1>{{ $title }}</h1>
    </div>

    <form action="{{ route('activities.store') }}" method="POST" enctype="multipart/form-data" class="flex flex-col w-full gap-6 text-base font-normal text-txt-base-default">

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="iku" :value="__('IKU')" />
            <div class="flex flex-row gap-2 items-center w-full">
                <x-select-input id="iku" name="iku" class="block w-full">
                    <option value="" disabled selected>Pilih IKU</option>
                    @foreach ($ikus as $iku)
                        <option value="{{ $iku->id }}" @selected(collect(old('iku_ids'))->contains($iku_id))>{{ $iku->kode }} - {{ $iku->nama }}</option>
                    @endforeach
                </x-select-input>
                <x-heroicon-m-information-circle class="w-[24px] h-[24px]" />
            </div>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="kegiatan" :value="__('Kegiatan')" />
            <x-text-input id="kegiatan" class="block w-full" type="text" name="kegiatan" :value="old('kegiatan')"
                required autofocus autocomplete="kegiatan" />
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="tindak_lanjut" :value="__('Tindak Lanjut')" />
            <x-textarea-input id="tindak_lanjut" class="block w-full h-20" name="tindak_lanjut"
                required>{{ old('tindak_lanjut') }}</x-textarea-input>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="kendala" :value="__('Kendala')" />
            <x-textarea-input id="kendala" class="block w-full h-20" name="kendala"
                required>{{ old('kendala') }}</x-textarea-input>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="percepatan" :value="__('Percepatan')" />
            <x-textarea-input id="percepatan" class="block w-full h-20" name="percepatan"
                required>{{ old('percepatan') }}</x-textarea-input>
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="file_upload" :value="__('Data Pendukung & Nota Dinas (Opsional)')" />
            <x-file-input id="file_upload" class="block w-full" name="file_upload" />
        </div>

        <div class="flex justify-end">
            <x-primary-button>
                {{ __($buttonLabel) }}
            </x-primary-button>
        </div>
    </form>
</div>
