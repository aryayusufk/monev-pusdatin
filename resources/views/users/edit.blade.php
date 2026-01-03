<x-app-layout>

  <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-dvh min-h-0 overflow-hidden">
    <x-users-form :title="'Ubah Pengguna'" :buttonLabel="'Simpan Perubahan'" :roles="$roles" :bidangs="$bidangs" :user="$user"></x-users-form>
  </div>
</x-app-layout>