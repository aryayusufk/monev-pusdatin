<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Bidang
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">

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
                <form method="POST" action="{{ route('bidangs.update', $bidang) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Kode Bidang
                        </label>
                        <input type="text" name="kode"
                               value="{{ old('kode', $bidang->kode) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700">
                            Nama Bidang
                        </label>
                        <input type="text" name="nama"
                               value="{{ old('nama', $bidang->nama) }}"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi" rows="3"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">{{ old('deskripsi', $bidang->deskripsi) }}</textarea>
                    </div>

                    <div class="flex justify-end space-x-2">
                        <a href="{{ route('bidangs.index') }}"
                           class="px-4 py-2 bg-gray-200 text-sm rounded-md">
                            Batal
                        </a>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-sm text-white rounded-md">
                                Update
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
