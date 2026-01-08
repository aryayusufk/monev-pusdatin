<x-app-layout>

  <div class="flex flex-col w-full min-h-full p-6 gap-6 items-center justify-center bg-bg-base-secondary">
    <x-activities-form :title="'Ubah Laporan Kegiatan'" :buttonLabel="'Simpan Perubahan'" :ikus="$ikus" :activity="$activity"></x-activities-form>
  </div>
</x-app-layout>