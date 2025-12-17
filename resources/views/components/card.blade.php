@props(['title' => '', 'subtitle' => '', 'href' => '#'])

<a href="{{ $href }}">
    <div class="flex flex-row px-4 py-3 gap-3 w-full bg-bg-base-default rounded-lg">
        <div class="flex flex-col gap-1 w-full">
            <p class="text-base font-normal text-txt-base-default">{{ $title }}</p>
            <p class="text-sm font-normal text-txt-base-secondary">{{ $subtitle }}</p>
        </div>

        <x-icon-button class="bg-acc-base-default">
            <x-heroicon-m-arrow-down-tray class="w[20px] h-[20px]" />
        </x-icon-button>
    </div>
</a>
