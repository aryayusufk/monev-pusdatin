<x-app-layout>
    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary">
        <x-table subheading="Lacak aktivitas pengguna, termasuk tindakan, stempel waktu, dan detail."
            heading="Catatan Aktivitas" :columns="$columns" :rows="$rows" :showCheckbox="false" :showActions="false"></x-table>
    </div>
</x-app-layout>
