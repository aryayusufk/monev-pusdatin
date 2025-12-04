<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Bidang
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-4 flex justify-end">
                <a href="{{ route('bidangs.create') }}"
                   class="px-4 py-2 bg-blue-600 text-white rounded text-sm">
                    + Pembuatan Bidang
                </a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Kode</th>
                            <th class="text-left py-2">Nama Bidang</th>
                            <th class="text-left py-2">Deskripsi</th>
                            <th class="text-right py-2">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($bidangs as $b)
                            <tr class="border-b">
                                <td class="py-2">{{ $b->kode }}</td>
                                <td>{{ $b->nama }}</td>
                                <td>{{ $b->deskripsi }}</td>
                                <td class="text-right space-x-2">
                                    <a href="{{ route('bidangs.edit', $b) }}"
                                       class="text-yellow-600 text-xs">Edit</a>

                                    <form action="{{ route('bidangs.destroy', $b) }}"
                                          method="POST"
                                          class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus bidang ini beserta seluruh user bidangnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 text-xs">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">
                                    Belum ada bidang.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-3">
                    {{ $bidangs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
