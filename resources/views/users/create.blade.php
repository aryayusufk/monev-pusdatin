<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Tambah User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-3 py-2 rounded text-sm mb-4">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('users.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Nama</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">Bidang (jika perlu)</label>
                        <select name="bidang_id"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">-- Pilih Bidang --</option>
                            @foreach ($bidangs as $b)
                                <option value="{{ $b->id }}"
                                    {{ old('bidang_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->nama }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Wajib diisi jika role = User Bidang atau Monev Bidang.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Aktif?</label>
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="text-xs text-gray-600 ml-1">Centang untuk mengaktifkan user.</span>
                    </div>

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('users.index') }}"
                           class="px-4 py-2 bg-gray-200 text-sm rounded-md">Batal</a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-sm text-white rounded-md">Simpan</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
