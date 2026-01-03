<x-app-layout>

    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-dvh min-h-0 overflow-hidden h-dvh min-h-0 overflow-hidden">

        <div class="flex flex-row justify-between items-center w-full">
            <div class="flex flex-row gap-6 items-center">
                <x-icon-button onclick="history.back()">
                    <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
                </x-icon-button>
                <h1 class="text-2xl font-semibold text-acc-base-default">Manajemen IKU</h1>
            </div>

            <a href="{{ route('ikus.create') }}">
            <x-primary-button><x-heroicon-m-plus class="w-[16px] h-auto" />Tambah IKU</x-primary-button>
            </a>
        </div>

        <x-table class="flex-1 min-h-0" subheading="" heading="" :columns="$columns" :rows="$rows" :showCheckbox="false"
            :showActions="true" editRoute="ikus.edit" deleteRoute="ikus.destroy"></x-table>

    </div>

</x-app-layout>
