<x-app-layout>

  <div class="flex flex-col items-center justify-center p-6 gap-6 bg-bg-base-secondary">
    <x-users-form :title="'Ubah Pengguna'" :buttonLabel="'Simpan Perubahan'" :roles="$roles" :bidangs="$bidangs" :user="$user"></x-users-form>
  </div>
</x-app-layout>