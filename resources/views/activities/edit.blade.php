<x-app-layout>

  <div class="flex flex-col p-6 gap-6 items-center justify-center bg-bg-base-secondary h-screen max-h-screen">
    <x-report-form :title="'Ubah Laporan Kegiatan'" :buttonLabel="'Simpan Perubahan'" :ikus="$ikus" :activity="$activity"></x-report-form>
  </div>
</x-app-layout>