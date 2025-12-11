<x-app-layout>
    <div class="max-w-6xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Log Aktivitas Sistem</h1>
                <p class="text-sm text-gray-500">Halaman ini hanya untuk testing, nanti boleh kamu hapus.</p>
            </div>

            <form method="GET" action="{{ route('logs.index') }}" class="flex items-center gap-2">
                <input
                    type="text"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    placeholder="Cari di detail..."
                    class="border-gray-300 rounded-md shadow-sm text-sm"
                />
                <input
                    type="text"
                    name="action"
                    value="{{ $filters['action'] ?? '' }}"
                    placeholder="Filter aksi (mis: user.created)"
                    class="border-gray-300 rounded-md shadow-sm text-sm"
                />
                <button
                    type="submit"
                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-semibold rounded-md text-white bg-blue-600 hover:bg-blue-700"
                >
                    Filter
                </button>
            </form>
        </div>

        @if ($logs->count() === 0)
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded">
                Belum ada log tercatat. Coba buat user/IKU/laporan dulu.
            </div>
        @else
            <div class="overflow-x-auto bg-white shadow-sm rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            {{-- Aksi = kode aksi yang kamu simpan, mis: user.created, iku.created --}}
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Aksi
                            </th>
                            {{-- Timestamp = waktu kejadian --}}
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Timestamp
                            </th>
                            {{-- Detail = kalimat deskripsi yang kamu isi di SystemLog::write() --}}
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Detail
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($logs as $log)
                            <tr>
                                {{-- Aksi --}}
                                <td class="px-4 py-2 whitespace-nowrap text-xs font-mono text-gray-700">
                                    {{ $log->action }}
                                </td>
                                {{-- Timestamp --}}
                                <td class="px-4 py-2 whitespace-nowrap text-xs text-gray-500">
                                    {{ $log->created_at->format('Y-m-d H:i:s') }}
                                </td>
                                {{-- Detail --}}
                                <td class="px-4 py-2 text-sm text-gray-800">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
