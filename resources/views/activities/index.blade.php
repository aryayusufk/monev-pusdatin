<x-app-layout>

  <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-screen max-h-screen">
    <div class="flex flex-row justify-between items-center w-full">
      <div class="flex flex-row gap-6 items-center">
        <x-icon-button onclick="history.back()">
          <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
        </x-icon-button>
        <h1 class="text-2xl font-semibold text-acc-base-default">Laporan Minggu Ke-1 Bidang 1</h1>
      </div>
    </div>

    <x-table subheading="" heading="" :columns="$columns" :rows="$rows" :showCheckbox="true"
      :showActions="true" editRoute="activities.edit" deleteRoute="activities.destroy"></x-table>
  </div>
</x-app-layout>