{{-- resources/views/activities/index.blade.php --}}
<x-app-layout>
  <div class="flex h-full min-h-screen">

    {{-- Main --}}
    <main class="flex-1 bg-gray-50 p-6">
      {{-- Header --}}
      <div class="flex items-start justify-between mb-6">
        <h1 class="text-2xl font-semibold text-sky-400">Laporan Mingguan Bidang</h1>

        <div class="flex items-center gap-6">
          <div class="flex gap-4">
            <x-select-input class="!px-10 !py-2 !text-left">
            <option value="">Tahun</option>
            </x-select-input>

            <x-select-input class="!px-10 !py-2 !text-left">
            <option value="">Periode</option>
            </x-select-input>

            <x-select-input class="!px-10 !py-2 !text-left">
            <option value="">Value</option>
            </x-select-input>
          </div>

          <x-primary-button class="!px-3 !py-2">Unduh Laporan Bulanan</x-primary-button>
        </div>
      </div>

      {{-- Filter --}}
      <div class="flex items-center gap-6 mb-6">
        <div class="w-56">
          <x-select-input id="iku" name="iku" class="w-full">
            <option value=""> Pilih IKU </option>
          </x-select-input>
        </div>
        <div class="flex-1"></div>
      </div>

      {{-- List (pakai card + x-icon-button) --}}
      <div class="space-y-3">
        @for ($i = 1; $i <= 5; $i++)
          <div class="bg-white rounded-lg border shadow-sm flex items-center justify-between px-4 py-3">
            <div>
              <div class="text-sm font-medium">Laporan Minggu Ke - {{ $i }}</div> 
              <div class="text-xs text-gray-500 mt-0.5">Tanggal</div>
            </div>

            {{-- gunakan x-icon-button (pastikan komponen menerima $attributes) --}}
            <x-icon-button class="w-10 h-10 rounded-full bg-sky-600 text-white hover:bg-sky-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
              </svg>
            </x-icon-button>
          </div>
        @endfor
      </div>
    </main>
  </div>
</x-app-layout>
