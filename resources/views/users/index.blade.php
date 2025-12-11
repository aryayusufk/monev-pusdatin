<x-app-layout>

    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-screen max-h-screen">

        <div class="flex flex-row justify-between items-center w-full">
            <div class="flex flex-row gap-6 items-center">
                <x-icon-button onclick="history.back()">
                    <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
                </x-icon-button>
                <h1 class="text-2xl font-semibold text-acc-base-default">Manajemen Pengguna</h1>
            </div>

            <a href="{{ route('users.create') }}">
            <x-primary-button><x-heroicon-m-plus class="w-[16px] h-auto" />Tambah Pengguna</x-primary-button>
            </a>
        </div>

        <x-table subheading="" heading="" :columns="$columns" :rows="$rows" :showCheckbox="false"
            :showActions="true" editRoute="users.edit" deleteRoute="users.destroy"></x-table>

    </div>


</x-app-layout>
