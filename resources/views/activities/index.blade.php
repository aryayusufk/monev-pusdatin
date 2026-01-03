<x-app-layout>
    <div class="flex flex-col p-6 gap-6 bg-bg-base-secondary h-dvh min-h-0 overflow-hidden">
        <div class="flex flex-row justify-between items-center w-full">
            <div class="flex flex-row gap-6 items-center">
                <x-icon-button onclick="history.back()">
                    <x-heroicon-m-arrow-left class="w-[20px] h-auto" />
                </x-icon-button>
                <h1 class="text-2xl font-semibold text-acc-base-default">
                    {{ $pageTitle ?? 'Laporan Kegiatan' }}
                </h1>
            </div>

            @php
                $canGlobal =
                    auth()->check() && (auth()->user()->hasRole('super_admin') || auth()->user()->hasRole('tim_monev'));
            @endphp


            <div id="bulkActionBar" class="hidden flex items-center gap-2">
                @if (!($monevMode ?? false))
                    <button type="submit" form="bulkSelectForm"
                        class="px-3 py-2 rounded-md bg-acc-base-default text-white text-sm font-medium">
                        Tambahkan ke Laporan Monitoring & Evaluasi
                    </button>
                @else
                    <button type="submit" form="bulkSelectForm"
                        class="px-3 py-2 rounded-md bg-red-600 text-white text-sm font-medium">
                        Keluarkan dari Laporan Monitoring & Evaluasi
                    </button>
                @endif

                <button type="button" id="bulkClear"
                    class="px-3 py-2 rounded-md border border-bor-base-default text-sm">
                    Batal
                </button>
            </div>
        </div>

        @php
            $isMonev = (bool) ($monevMode ?? false);
            $bulkAction = $isMonev ? route('activities.bulk-remove-monev') : route('activities.bulk-add-monev');
        @endphp

        <form id="bulkSelectForm" method="POST" action="{{ $bulkAction }}">
            @csrf
            <div id="bulkHiddenInputs"></div>
        </form>

        <x-table class="flex-1 min-h-0" id="activitiesTable" subheading="" heading="" :columns="$columns" :rows="$rows"
            :showCheckbox="true" :showActions="true" checkboxName="selected" editRoute="activities.edit"
            deleteRoute="activities.destroy" />
    </div>

    <script>
        (function() {
            const hiddenWrap = document.getElementById('bulkHiddenInputs');
            const tableId = 'activitiesTable';

            const bar = document.getElementById('bulkActionBar');
            const clearBtn = document.getElementById('bulkClear');

            function setBarVisible(visible) {
                if (!bar) return;
                bar.classList.toggle('hidden', !visible);
            }

            function clearSelectionUI() {
                const root = document.getElementById(tableId);
                if (!root) return;

                // uncheck semua row checkbox
                root.querySelectorAll('input[data-role="row-checkbox"]').forEach(cb => cb.checked = false);

                // reset checkbox header (select-all)
                const header = root.querySelector('input[data-role="select-all"]');
                if (header) {
                    header.checked = false;
                    header.indeterminate = false;
                }
            }

            // setiap selection berubah:
            // 1) isi hidden input selected[]
            // 2) tampilkan/sembunyikan action bar
            window.addEventListener('table-selection-changed', function(e) {
                if (!e.detail || e.detail.tableId !== tableId) return;

                const values = e.detail.values || [];

                // isi hidden inputs
                hiddenWrap.innerHTML = '';
                values.forEach(function(v) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected[]';
                    input.value = v;
                    hiddenWrap.appendChild(input);
                });

                // toggle action bar
                setBarVisible(values.length > 0);
            });

            // tombol Batal
            clearBtn?.addEventListener('click', function() {
                hiddenWrap.innerHTML = '';
                clearSelectionUI();
                setBarVisible(false);
            });
        })();
    </script>
</x-app-layout>
