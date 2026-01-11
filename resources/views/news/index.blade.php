<x-app-layout>
    <div class="flex flex-col w-full min-h-full p-6 gap-6 items-center justify-center bg-bg-base-secondary">
        <x-running-text-form
            :title="'Manajemen Berita'"
            :subtitle="'Atur teks yang akan ditampilkan pada halaman login.'"
            :buttonLabel="'Simpan Perubahan'"
            :enabled="$enabled"
            :content="$content"
        />
    </div>
</x-app-layout>
