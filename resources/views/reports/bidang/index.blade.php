<x-app-layout>

    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary">
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

                <x-select-input name="value" class="w-full" onchange="this.form.submit()" :disabled="!$periodSelected">
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
                $selectedYear = (int) request('year', now()->year);
                $period = request('period'); // 'month' | 'quarter'
                $value = request('value'); // 1..12 | 1..4

                $carry = array_filter(
                    [
                        'bidang_id' => request('bidang_id'),
                        'iku_id' => request('iku_id'),
                    ],
                    fn($v) => filled($v),
                );

                $buttonLabel = null;
                $buttonHref = null;

                if ($period === 'month' && filled($value)) {
                    $buttonLabel = 'Unduh Laporan Bulanan';
                    $buttonHref = route(
                        'reports.bidang.export.monthly',
                        array_merge($carry, [
                            'year' => $selectedYear,
                            'month' => (int) $value,
                        ]),
                    );
                }

                if ($period === 'quarter' && filled($value)) {
                    $buttonLabel = 'Unduh Laporan Triwulan';
                    $buttonHref = route(
                        'reports.bidang.export.quarterly',
                        array_merge($carry, [
                            'year' => $selectedYear,
                            'quarter' => (int) $value,
                        ]),
                    );
                }
            @endphp

            {{-- ✅ TOMBOL HANYA MUNCUL JIKA FILTER LENGKAP --}}
            @if ($buttonHref)
                <a href="{{ $buttonHref }}">
                    <x-primary-button>
                        {{ $buttonLabel }}
                    </x-primary-button>
                </a>
            @endif
        </div>


        <div class="flex flex-col gap-3 w-full items-center">
            @forelse ($cards as $card)
                @php
                    $baseParams = array_filter(
                        [
                            'bidang_id' => request('bidang_id'),
                            'iku_id' => request('iku_id'),
                        ],
                        fn($v) => filled($v),
                    );

                    $detailParams = array_merge($baseParams, [
                        'start' => $card['start'],
                        'end' => $card['end'],
                        'title' => $card['title'],
                    ]);

                    $href = route('activities.index', $detailParams);

                    $downloadUrl = null;

                    if (request('period') === 'month') {
                        $downloadUrl = route('reports.bidang.export.weekly', $detailParams);
                    }

                    if (request('period') === 'quarter') {
                        $downloadUrl = route(
                            'reports.bidang.export.monthly',
                            array_merge($baseParams, [
                                'year' => (int) request('year', now()->year),
                                'month' => (int) ($card['month'] ?? 0),
                                'title' => $card['title'],
                            ]),
                        );
                    }
                @endphp

                <x-card :href="$href" :downloadUrl="$downloadUrl" :title="$card['title']" :subtitle="$card['subtitle']" />
            @empty
                <div class="text-sm text-txt-base-default">
                    Pilih Tahun, Periode, dan Value untuk menampilkan daftar laporan.
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
