<x-app-layout>

  <div class="">
    <x-users-form :title="'Ubah Pengguna'" :buttonLabel="'Simpan Perubahan'" :roles="$roles" :bidangs="$bidangs" :user="$user"></x-users-form>
  </div>
</x-app-layout>