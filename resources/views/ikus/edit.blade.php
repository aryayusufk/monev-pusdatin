<x-app-layout>

  <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-dvh min-h-0 overflow-hidden">
    <x-ikus-form :title="'Ubah IKU'" :buttonLabel="'Simpan Perubahan'" :bidangs="$bidangs" :iku="$iku" :selectedBidangIds="$selectedBidangIds"></x-ikus-form>
  </div>
</x-app-layout>