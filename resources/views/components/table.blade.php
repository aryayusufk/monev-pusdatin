@props([
    'subheading' => 'Subheading',
    'heading' => 'Heading',
    'columns' => [],
    'rows' => [],
    'showCheckbox' => false,
    'showActions' => false,
    'checkboxName' => 'selected',
])

<div class="flex flex-col bg-bg-base-default border border-bor-base-default rounded-lg shadow-sm h-full min-h-0">

    @if ($heading || $subheading)
        <div class="flex flex-col px-4 pt-2 pb-1">
            @if ($subheading)
                <p class="text-sm font-normal text-txt-base-secondary">{{ $subheading }}</p>
            @endif
            @if ($heading)
                <h1 class="text-base font-semibold text-acc-base-default">{{ $heading }}</h1>
            @endif
        </div>
    @endif

    <div class="flex flex-col flex-1 gap-2 min-h-0">
        <!-- Table Header -->
        <table class="table-fixed w-full">
            <thead>
                <tr class="flex flex-row flex-1 p-4 gap-4 border-b border-bor-base-default">
                    @if ($showCheckbox)
                        <th class="shrink-0 w-6 flex items-center justify-center">
                            <input type="checkbox"
                                class="w-4 h-4 rounded border-bor-base-default text-acc-base-default focus:ring-acc-base-default">
                        </th>
                    @endif

                    @foreach ($columns as $column)
                        <th class="w-full text-left text-sm font-semibold text-txt-base-primary">
                            {{ $column }}
                        </th>
                    @endforeach

                    @if ($showActions)
                        <th class="shrink-0 w-8"></th>
                    @endif
                </tr>
            </thead>
        </table>

        <!-- Table Body -->
        <div class="flex-1 min-h-0 overflow-y-auto">
            <table class="table-fixed w-full">
                <tbody>
                    @forelse($rows as $rowIndex => $row)
                        <tr
                            class="flex flex-row flex-1 gap-4 px-4 py-4 border-b border-bor-base-default last:border-b-0">
                            @if ($showCheckbox)
                                <td class="shrink-0 w-6 flex items-start justify-center pt-1">
                                    <input type="checkbox" name="{{ $checkboxName }}[]"
                                        value="{{ $row['id'] ?? $rowIndex }}"
                                        class="w-4 h-4 rounded border-bor-base-default text-acc-base-default focus:ring-acc-base-default">
                                </td>
                            @endif

                            {{-- Sel data --}}
                            @foreach ($row as $key => $cell)
                                @continue($key === 'id') {{-- id hanya dipakai untuk value checkbox / action --}}
                                <td class="w-full text-justify text-sm leading-relaxed text-txt-base-primary">
                                    {{-- 
                                        Pakai {!! !!} supaya bisa kirim HTML dari controller
                                        (misal untuk link "Nama File").
                                        Kalau mau aman full text, ganti ke {{ $cell }}
                                    --}}
                                    {!! $cell !!}
                                </td>
                            @endforeach

                            @if ($showActions)
                                <td class="shrink-0 w-8 flex items-start justify-end">
                                    <button type="button"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-full hover:bg-bg-base-subtle focus:outline-none focus:ring-2 focus:ring-acc-base-default">
                                        <span class="sr-only">Buka menu</span>
                                        {{-- ikon titik tiga (horizontal) --}}
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                            class="w-4 h-4 text-txt-base-secondary">
                                            <circle cx="4" cy="10" r="1.5" />
                                            <circle cx="10" cy="10" r="1.5" />
                                            <circle cx="16" cy="10" r="1.5" />
                                        </svg>
                                    </button>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td class="px-4 py-6 text-center text-sm text-txt-base-secondary"
                                colspan="{{ count($columns) + ($showCheckbox ? 1 : 0) + ($showActions ? 1 : 0) }}">
                                Tidak ada data yang dapat ditampilkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
