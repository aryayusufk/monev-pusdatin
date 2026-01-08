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

    // Build grid template: dynamic columns = 1fr, checkbox/actions = fixed width
    $gridParts = array_fill(0, count($columns), 'minmax(0, 1fr)');

    if ($showCheckbox) $gridParts[] = '24px'; // w-6
    if ($showActions)  $gridParts[] = '64px'; // space for "Ubah/Hapus" column

    $gridTemplate = implode(' ', $gridParts);
@endphp

<div id="{{ $tableId }}"
     class="flex flex-col bg-bg-base-default border border-bor-base-default rounded-lg shadow-sm flex-1 min-h-0">

    {{-- Header --}}
    <div class="border-b border-bor-base-default">
        <div class="grid items-center gap-4 p-4"
             style="grid-template-columns: {{ $gridTemplate }};">
            @foreach ($columns as $column)
                <div class="text-left text-sm font-semibold text-txt-base-primary">
                    {{ $column }}
                </div>
            @endforeach

            @if ($showCheckbox)
                <div class="flex items-center justify-center">
                    <input type="checkbox" data-role="select-all" aria-label="Pilih semua"
                           class="w-4 h-4 rounded border-bor-base-default text-acc-base-default focus:ring-acc-base-default">
                </div>
            @endif

            @if ($showActions)
                <div class="text-left text-sm font-semibold text-txt-base-primary">
                    {{-- bisa kosong atau isi "Aksi" --}}
                </div>
            @endif
        </div>
    </div>

    {{-- Body --}}
    <div class="flex-1 min-h-0 overflow-y-auto">
        @forelse($rows as $rowIndex => $row)
            <div class="grid gap-4 px-4 py-4 border-b border-bor-base-default last:border-b-0"
                 style="grid-template-columns: {{ $gridTemplate }};">

                @foreach ($row as $key => $cell)
                    @continue($key === 'id')
                    <div class="text-left text-sm leading-relaxed text-txt-base-primary whitespace-normal break-words">
                        {!! $cell !!}
                    </div>
                @endforeach

                @if ($showCheckbox)
                    <div class="flex items-start justify-center pt-1">
                        <input type="checkbox" name="{{ $checkboxName }}[]"
                               value="{{ $row['id'] ?? $rowIndex }}" data-role="row-checkbox"
                               class="w-4 h-4 rounded border-bor-base-default text-acc-base-default focus:ring-acc-base-default">
                    </div>
                @endif

                @if ($showActions)
                    <div class="flex flex-col items-end justify-end gap-2">
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
                    </div>
                @endif
            </div>
        @empty
            <div class="px-4 py-6 text-center text-sm text-txt-base-secondary">
                Tidak ada data yang dapat ditampilkan.
            </div>
        @endforelse
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
