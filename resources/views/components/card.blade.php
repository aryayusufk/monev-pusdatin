@props(['title' => '', 'subtitle' => '', 'href' => '#', 'downloadUrl' => ''])

<a href="{{ $href }}" class="w-full">
    <div class="flex flex-row px-4 py-3 gap-3 w-full bg-bg-base-default rounded-lg">
        <div class="flex flex-col gap-1 w-full">
            <p class="text-sm font-normal text-txt-base-default">{{ $title }}</p>
            <p class="text-sm font-normal text-txt-base-secondary">{{ $subtitle }}</p>
        </div>

        @if ($downloadUrl)
            <a href="{{ $downloadUrl }}">
                <x-icon-button class="bg-acc-base-default" title="Unduh PDF">
                    <x-heroicon-m-arrow-down-tray class="w-[20px] h-auto" />
                </x-icon-button>
            </a>
        @endif
    </div>
</a>
