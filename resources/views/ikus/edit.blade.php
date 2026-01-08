<x-app-layout>

  <div class="flex flex-col w-full min-h-full p-6 gap-6 items-center justify-center bg-bg-base-secondary">
    <x-ikus-form :title="'Ubah IKU'" :buttonLabel="'Simpan Perubahan'" :bidangs="$bidangs" :iku="$iku" :selectedBidangIds="$selectedBidangIds"></x-ikus-form>
  </div>
</x-app-layout>