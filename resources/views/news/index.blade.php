<x-app-layout>
    <div class="flex flex-col w-full min-h-full p-6 gap-6 items-center justify-center bg-bg-base-secondary">
        <x-running-text-form
            :title="'Pengaturan Running Text Login'"
            :buttonLabel="'Simpan Perubahan'"
            :enabled="$enabled"
            :content="$content"
        />
    </div>
</x-app-layout>
