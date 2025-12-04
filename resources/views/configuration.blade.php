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
            <a href="{{ route('ikus.index') }}" class="flex flex-1">
                <div
                    class="min-w-[240px] flex flex-col justify-center items-center bg-bg-base-default border border-bor-base-default rounded-lg p-6 gap-6">
                    <x-heroicon-m-arrow-trending-up class="w-[40px] h-auto" />
                    <p class="w-full text-center text-2xl font-semibold">Manajemen IKU</p>
                </div>
            </a>
            <x-table subheading="Daftar IKU (Indikator Kinerja Utama) aktif." heading="IKU (Indikator Kinerja Utama)"
                :columns="$ikuColumns" :rows="$ikuRows" :showCheckbox="false" :showActions="false"></x-table>
        </div>
        <div class="flex flex-row flex-1 gap-6 min-h-0">
            <x-table subheading="Daftar bidang (pengguna) aktif." heading="Bidang (Users)" :columns="$userColumns"
                :rows="$userRows" :showCheckbox="false" :showActions="false"></x-table>
            <a href="{{ route('users.index') }}" class="flex flex-1">
                <div
                    class="min-w-[240px] flex flex-col justify-center items-center bg-bg-base-default border border-bor-base-default rounded-lg p-6 gap-6">
                    <x-heroicon-m-users class="w-[40px] h-auto" />
                    <p class="w-full text-center text-2xl font-semibold">Manajemen User</p>
                </div>
            </a>
        </div>
    </div>
</x-app-layout>
