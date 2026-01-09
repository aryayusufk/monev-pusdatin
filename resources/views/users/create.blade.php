<x-app-layout>

    <div class="flex flex-col w-full min-h-full p-6 gap-6 items-center justify-center bg-bg-base-secondary">
        <x-users-form :title="'Pembuatan Pengguna'" :buttonLabel="'Buat Pengguna'" :roles="$roles" :bidangs="$bidangs" :user="null"></x-users-form>
    </div>
</x-app-layout>
