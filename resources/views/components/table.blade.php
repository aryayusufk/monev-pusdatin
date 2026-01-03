@props([
    'subheading' => 'Subheading',
    'heading' => 'Heading',
    'columns' => [],
    'rows' => [],
    'showCheckbox' => false,
    'showActions' => false,
    'checkboxName' => 'selected',
    'editRoute' => null,
    'deleteRoute' => null,
])

@php
    $tableId = $attributes->get('id') ?? 'tbl_' . substr(md5(uniqid('', true)), 0, 8);
@endphp

<div id="{{ $tableId }}"
    class="flex flex-col bg-bg-base-default border border-bor-base-default rounded-lg shadow-sm flex-1 min-h-0">

    <div class="flex flex-col flex-1 gap-2 min-h-0">
        <table class="table-fixed w-full">
            <thead>
                <tr class="flex flex-row flex-1 items-center p-4 gap-4 border-b border-bor-base-default">
                    @foreach ($columns as $column)
                        <th class="flex-1 text-left text-sm font-semibold text-txt-base-primary">
                            {{ $column }}
                        </th>
                    @endforeach

                    @if ($showCheckbox)
                        <th class="shrink-0 w-6 flex items-center justify-center">
                            <input type="checkbox" data-role="select-all" aria-label="Pilih semua"
                                class="w-4 h-4 rounded border-bor-base-default text-acc-base-default focus:ring-acc-base-default">
                        </th>
                    @endif

                    @if ($showActions)
                        <th class="shrink-0 w-8"></th>
                    @endif
                </tr>
            </thead>
        </table>

        <div class="flex-1 min-h-0 overflow-y-auto">
            <table class="table-fixed w-full">
                <tbody>
                    @forelse($rows as $rowIndex => $row)
                        <tr
                            class="flex flex-row flex-1 gap-4 px-4 py-4 border-b border-bor-base-default last:border-b-0">
                            @foreach ($row as $key => $cell)
                                @continue($key === 'id')
                                <td class="flex-1 text-justify text-sm leading-relaxed text-txt-base-primary">
                                    {!! $cell !!}
                                </td>
                            @endforeach

                            @if ($showCheckbox)
                                <td class="shrink-0 w-6 flex items-start justify-center pt-1">
                                    <input type="checkbox" name="{{ $checkboxName }}[]"
                                        value="{{ $row['id'] ?? $rowIndex }}" data-role="row-checkbox"
                                        class="w-4 h-4 rounded border-bor-base-default text-acc-base-default focus:ring-acc-base-default">
                                </td>
                            @endif

                            @if ($showActions)
                                <td class="shrink-0 w-8 flex flex-col items-center justify-end gap-2">
                                    @if ($editRoute)
                                        <a href="{{ route($editRoute, $row['id']) }}"
                                            class="text-xs font-medium text-acc-base-default hover:underline">
                                            Ubah
                                        </a>
                                    @endif

                                    @if ($deleteRoute)
                                        <form action="{{ route($deleteRoute, $row['id']) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs font-medium text-red-600 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
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

@if ($showCheckbox)
    <script>
        (function() {
            const root = document.getElementById(@json($tableId));
            if (!root) return;

            const selectAll = root.querySelector('input[data-role="select-all"]');

            function rowCheckboxes() {
                return Array.from(root.querySelectorAll('input[data-role="row-checkbox"]'));
            }

            function dispatchSelectionChanged() {
                const boxes = rowCheckboxes();
                const values = boxes.filter(cb => cb.checked).map(cb => cb.value);

                window.dispatchEvent(new CustomEvent('table-selection-changed', {
                    detail: {
                        tableId: root.id,
                        selected: values.length,
                        total: boxes.length,
                        values,
                    }
                }));
            }

            function syncHeaderState() {
                if (!selectAll) return;

                const boxes = rowCheckboxes();
                const total = boxes.length;
                const selected = boxes.filter(cb => cb.checked).length;

                if (total === 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                    return;
                }

                if (selected === 0) {
                    selectAll.checked = false;
                    selectAll.indeterminate = false;
                } else if (selected === total) {
                    selectAll.checked = true;
                    selectAll.indeterminate = false;
                } else {
                    selectAll.checked = false;
                    selectAll.indeterminate = true;
                }
            }

            selectAll?.addEventListener('change', function() {
                const checked = !!this.checked;
                rowCheckboxes().forEach(cb => cb.checked = checked);
                syncHeaderState();
                dispatchSelectionChanged();
            });

            root.addEventListener('change', function(e) {
                const t = e.target;
                if (t && t.matches('input[data-role="row-checkbox"]')) {
                    syncHeaderState();
                    dispatchSelectionChanged();
                }
            });

            syncHeaderState();
            dispatchSelectionChanged();
        })();
    </script>
@endif
