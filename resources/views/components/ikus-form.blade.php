@props([
    'title' => 'Form Title',
    'buttonLabel' => 'Button Label',
    'bidangs' => [],
])

<div class="flex flex-col w-full p-6 gap-6 rounded-lg bg-bg-base-default border border-bor-base-default">
            <div class="flex flex-row w-full justify-between">
                <x-icon-button onclick="history.back()">
                    <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
                </x-icon-button>
                <h1 class="text-2xl font-semibold text-acc-base-default">{{ $title }}</h1>
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
                  <x-input-label for="target" :value="__('Target')" />
                  <x-text-input id="target" class="block w-full" type="number" name="target" :value="old('target')"
                      required autofocus autocomplete="target" />
                </div>

                <div class="flex flex-col gap-2 w-full">
                  <x-input-label for="bidang_id" :value="__('Bidang')" />
                  <x-select-input id="bidang_id" name="bidang_id" class="block w-full">
                      <option value="" disabled selected>Pilih Bidang</option>
                      @foreach ($bidangs as $bidang)
                          <option value="{{ $bidang->id }}" @selected(old('bidang_id') == $bidang->id)>{{ $bidang->nama }}</option>
                      @endforeach
                  </x-select-input>
                </div>

                <div class="flex justify-end">
                    <x-primary-button>
                        {{ __($buttonLabel) }}
                    </x-primary-button>
                </div>
            </form>
        </div>