@props([
    'title' => 'Pengaturan Running Text Login',
    'buttonLabel' => 'Simpan',
    'enabled' => false,
    'content' => '',
])

<div class="flex flex-col w-full p-6 gap-6 rounded-lg bg-bg-base-default border border-bor-base-default">
    <div class="flex flex-row w-full justify-between">
        <x-icon-button onclick="history.back()">
            <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
        </x-icon-button>
        <h1 class="text-2xl font-semibold text-acc-base-default">{{ $title }}</h1>
    </div>

    @if (session('success'))
        <div class="p-3 rounded bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('news.index.update') }}" method="POST"
        class="flex flex-col w-full gap-6 text-base font-normal text-txt-base-default">
        @csrf

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="content" :value="__('Isi Running Text')" />
            <x-textarea-input id="content" class="block w-full h-24" name="content" maxlength="500">
                {{ old('content', $content) }}
            </x-textarea-input>

            @error('content')
                <div class="text-red-600 text-sm">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex flex-col gap-2 w-full">
            <x-input-label for="enabled" :value="__('Status Aktivasi')" />

            <input type="hidden" name="enabled" value="0">
            <input type="checkbox" id="enabled" name="enabled" value="1"
                @if (old('enabled', $enabled ? 1 : 0)) checked @endif
                class="w-4 h-4 rounded border-bor-base-default text-acc-base-default focus:ring-acc-base-default">
        </div>

        <div class="flex justify-end">
            <x-primary-button>
                {{ __($buttonLabel) }}
            </x-primary-button>
        </div>
    </form>
</div>
