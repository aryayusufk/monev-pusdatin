<x-app-layout>

    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-dvh min-h-0 overflow-hidden">

        <div class="flex flex-row justify-between items-center w-full">
            <h1 class="text-2xl font-semibold text-acc-base-default">Manajemen Pengguna</h1>

            <a href="{{ route('users.create') }}">
                <x-primary-button><x-heroicon-m-plus class="w-[16px] h-auto" />Tambah Pengguna</x-primary-button>
            </a>
        </div>

        <x-table class="flex-1 min-h-0" subheading="" heading="" :columns="$columns" :rows="$rows" :showCheckbox="false"
            :showActions="true" editRoute="users.edit" deleteRoute="users.destroy"></x-table>

    </div>


</x-app-layout>
