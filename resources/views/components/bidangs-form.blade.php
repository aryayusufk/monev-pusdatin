@props([
    'title' => 'Form Title',
    'buttonLabel' => 'Button Label',
    'bidang' => null,
])

@php
    $isEdit = isset($bidang) && $bidang;
@endphp

<div class="flex flex-col w-full p-6 gap-6 rounded-lg bg-bg-base-default border border-bor-base-default">
    <div class="flex flex-row w-full justify-between">
        <x-icon-button onclick="history.back()">
            <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
        </x-icon-button>
        <h1 class="text-2xl font-semibold text-acc-base-default">{{ $title }}</h1>
    </div>

    <form action="{{ $isEdit ? route('bidangs.update', $bidang) : route('bidangs.store') }}" method="POST"
        enctype="multipart/form-data" class="flex flex-col w-full gap-6 text-base font-normal text-txt-base-default">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        @unless ($isEdit)
            <div class="flex flex-col gap-2 w-full">
                <x-input-label for="nama_bidang" :value="__('Nama Bidang')" />
                <x-text-input id="nama_bidang" class="block w-full" type="text" name="nama_bidang" :value="old('nama_bidang')"
                    required autofocus autocomplete="nama_bidang" />
            </div>

            <div class="flex flex-col gap-2 w-full">
                <x-input-label for="username" :value="__('Username')" />
                <x-text-input id="username" class="block w-full" type="text" name="username" :value="old('username')" required
                    autofocus autocomplete="username" />
            </div>

            <div class="flex flex-col gap-2 w-full">
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block w-full" type="password" name="password" required
                    autocomplete="new-password" />
            </div>
        @endunless

        @if ($isEdit)
            <div class="flex flex-col gap-2 w-full">
                <x-input-label for="kode" :value="__('Kode Bidang')" />
                <x-text-input id="kode" class="block w-full" type="text" name="kode" :value="old('kode', $bidang->kode)"
                    required autofocus autocomplete="off" />
            </div>

            <div class="flex flex-col gap-2 w-full">
                <x-input-label for="nama" :value="__('Nama Bidang')" />
                <x-text-input id="nama" class="block w-full" type="text" name="nama" :value="old('nama', $bidang->nama)"
                    required autofocus autocomplete="off" />
            </div>

            <div class="flex flex-col gap-2 w-full">
                <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                <x-textarea-input id="deskripsi" class="block w-full h-20"
                    name="deskripsi">{{ old('deskripsi', $bidang->deskripsi) }}</x-textarea-input>
            </div>
        @endif

        <div class="flex justify-end">
            <x-primary-button>
                {{ __($buttonLabel) }}
            </x-primary-button>
        </div>
    </form>
</div>
