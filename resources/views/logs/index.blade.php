<x-app-layout>
    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-dvh min-h-0 overflow-hidden h-dvh min-h-0 overflow-hidden">
        <x-table class="flex-1 min-h-0" subheading="Lacak aktivitas pengguna, termasuk tindakan, stempel waktu, dan detail."
            heading="Catatan Aktivitas" :columns="$columns" :rows="$rows" :showCheckbox="false" :showActions="false"></x-table>
    </div>
</x-app-layout>
