<x-app-layout>
    <!--
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    -->

    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-screen max-h-screen">
        <div class="flex flex-row flex-1 gap-6 min-h-0">
            <x-table subheading="Lacak aktivitas pengguna, termasuk tindakan, stempel waktu, dan detail."
                heading="Catatan Aktivitas"></x-table>
        </div>
        <div class="flex flex-row flex-1 gap-6 min-h-0">
            <x-card text="Manajemen IKU"></x-card>
            <x-table subheading="Daftar IKU (Indikator Kinerja Utama) aktif."
                heading="IKU (Indikator Kinerja Utama)"></x-table>
        </div>
        <div class="flex flex-row flex-1 gap-6 min-h-0">
            <x-table subheading="Daftar bidang (pengguna) aktif."
                heading="Bidang (Users)"></x-table>
            <x-card text="Manajemen Bidang"></x-card>
        </div>
    </div>
</x-app-layout>
