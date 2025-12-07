<x-app-layout>

    <div class="flex flex-col items-center justify-center p-6 gap-6 bg-bg-base-secondary h-screen max-h-screen">

        <div class="flex flex-col w-full p-6 gap-6 rounded-lg bg-bg-base-default border border-bor-base-default">
            <div class="flex flex-row w-full justify-between">
                <x-icon-button onclick="history.back()">
                    <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
                </x-icon-button>
                <h1 class="text-2xl font-semibold text-acc-base-default">Pembuatan IKU</h1>
            </div>

            <form action="{{ route('ikus.store') }}" method="POST" enctype="multipart/form-data"
                class="flex flex-col w-full gap-6 text-base font-normal text-txt-base-default">
                @csrf

                <div class="flex flex-col gap-2 w-full">
                  <x-input-label for="nama" :value="__('Judul IKU')" />
                  <x-text-input id="nama" class="block w-full" type="text" name="nama" :value="old('nama')"
                      required autofocus autocomplete="nama" />
                </div>

                <div class="flex flex-col gap-2 w-full">
                  <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                  <x-textarea-input id="deskripsi" class="block w-full h-20" name="deskripsi"
                      required>{{ old('deskripsi') }}</x-textarea-input>
                </div>

                <div class="flex flex-col gap-2 w-full">
                  <x-input-label for="tahun" :value="__('Tahun')" />
                  <x-text-input id="tahun" class="block w-full" type="number" name="tahun" :value="old('tahun')"
                      required autofocus autocomplete="tahun" required min="2000" max="2100"/>
                </div>

                <div class="flex flex-col gap-2 w-full">
                    <x-input-label for="bidang_ids" :value="__('Bidang Terkait')" />
                    <x-select-input id="bidang_ids" name="bidang_ids[]" class="block w-full" multiple>
                        @foreach ($bidangs as $bidang)
                            <option value="{{ $bidang->id }}"
                                @if (collect(old('bidang_ids', []))->contains($bidang->id)) selected @endif>
                                {{ $bidang->nama }}
                            </option>
                        @endforeach
                    </x-select-input>
                    <p class="text-xs text-txt-base-subtle mt-1">
                        (Tahan Ctrl / Cmd untuk memilih lebih dari satu bidang)
                    </p>
                </div>


                <div class="flex justify-end">
                    <x-primary-button>
                        {{ __('Buat IKU') }}
                    </x-primary-button>
                </div>
            </form>
        </div>

    </div>

</x-app-layout>
