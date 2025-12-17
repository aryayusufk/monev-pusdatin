<x-app-layout>

    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-screen max-h-screen">
        <div class="flex flex-row justify-between items-center w-full">
            <div class="flex grow">
                <h1 class="text-2xl font-semibold text-acc-base-default">Laporan Bidang</h1>
            </div>

            <form action="{{ route('reports.bidang.index') }}" id="filter-form" method="GET"
                class="flex flex-row grow gap-3 items-center">

                @if (request()->filled('bidang_id'))
                    <input type="hidden" name="bidang_id" value="{{ request('bidang_id') }}">
                @endif

                <x-select-input name="iku_id" class="w-full" onchange="this.form.submit()">
                    <option value="">IKU</option>
                    @foreach ($ikus as $iku)
                        <option value="{{ $iku->id }}" @selected((string) request('iku_id') === (string) $iku->id)>
                            {{ $iku->kode }} - {{ $iku->nama }}
                        </option>
                    @endforeach
                </x-select-input>

                <x-select-input name="year" onchange="this.form.submit()" class="w-full">
                    @foreach ($years as $y)
                        <option value="{{ $y }}" @selected((int) request('year', $year) === (int) $y)>
                            {{ $y }}
                        </option>
                    @endforeach
                </x-select-input>

                <x-select-input name="period" onchange="this.form.submit()" class="w-full">
                    <option value="">Periode</option>
                    <option value="month" @selected(request('period') === 'month')>Bulanan</option>
                    <option value="quarter" @selected(request('period') === 'quarter')>Triwulan</option>
                </x-select-input>

                @php
                    $periodSelected = filled(request('period'));
                @endphp

                <x-select-input name="value" class="w-full whitespace-normal" onchange="this.form.submit()"
                    :disabled="!$periodSelected">
                    <option value="">
                        {{ $periodSelected ? 'Value' : 'Pilih Periode terlebih dahulu' }}
                    </option>

                    @if ($periodSelected && request('period') === 'month')
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" @selected((string) request('value') === (string) $m)>
                                {{ \Carbon\Carbon::createFromDate((int) request('year', $year), $m, 1)->translatedFormat('F') }}
                            </option>
                        @endfor
                    @elseif ($periodSelected && request('period') === 'quarter')
                        @for ($q = 1; $q <= 4; $q++)
                            <option value="{{ $q }}" @selected((string) request('value') === (string) $q)>
                                Triwulan {{ $q }}
                            </option>
                        @endfor
                    @endif

                </x-select-input>

                <a href="{{ route('reports.bidang.index', request()->filled('bidang_id') ? ['bidang_id' => request('bidang_id')] : []) }}"
                    class="text-sm underline">Reset</a>
            </form>
        </div>

        <div class="flex flex-row justify-end">
            @php
                $selectedYear = (int) request('year', $year);
                $period = request('period');
                $value = request('value');

                // Query yang ingin selalu ikut saat pindah halaman / download
                $carry = array_filter(
                    [
                        'bidang_id' => request('bidang_id'),
                        'iku_id' => request('iku_id'),
                    ],
                    fn($v) => filled($v),
                );
            @endphp

            {{-- ✅ Tombol unduh diarahkan sesuai pilihan --}}
            @if ($period === 'month' && filled($value))
                <a
                    href="{{ route('reports.bidang.monthly.report', [$selectedYear, (int) $value]) . (count($carry) ? '?' . http_build_query($carry) : '') }}">
                    <x-primary-button>
                        Unduh Laporan Bulanan
                    </x-primary-button>
                </a>
            @else
                <x-primary-button disabled title="Pilih Periode Bulanan dan Bulan terlebih dahulu">
                    Unduh Laporan Bulanan
                </x-primary-button>
            @endif
        </div>

        <div class="flex flex-col gap-3 w-full">
            @forelse ($cards as $card)
                @php
                    $params = array_filter(
                        [
                            'bidang_id' => request('bidang_id'),
                            'iku_id' => request('iku_id'),
                            'start' => $card['start'],
                            'end' => $card['end'],
                            'title' => $card['title'],
                        ],
                        fn($v) => filled($v),
                    );
                @endphp

                <x-card :href="route('activities.index', $params)" :title="$card['title']" :subtitle="$card['subtitle']" />
            @empty
                <div class="text-sm text-txt-base-secondary">
                    Pilih Tahun, Periode, dan Value untuk menampilkan daftar laporan.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
