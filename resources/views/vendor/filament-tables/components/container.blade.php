<div
    {{
        $attributes->class([
            'fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10',
        ])
    }}
    style="position: relative;"
>
    {{ $slot }}

    {{-- Loading overlay: outer div để Livewire tự set display:none/block, KHÔNG inline display --}}
    <div
        wire:loading
        wire:target="tableFilters, tableSearch, tableColumnSearches, tableGrouping, tableSortColumn, gotoPage, nextPage, previousPage, tableRecordsPerPage, activeTab"
        style="position:absolute; inset:0; z-index:10; background:rgba(17,24,39,0.5);"
    >
        {{-- Inner div xử lý centering --}}
        <div style="height:100%; display:flex; align-items:center; justify-content:center;">
            <div style="display:flex; align-items:center; gap:10px; background:rgba(31,41,55,0.95); border:1px solid rgba(255,255,255,0.1); border-radius:12px; padding:10px 18px; box-shadow:0 4px 24px rgba(0,0,0,0.4);">
                <svg style="width:18px; height:18px; animation:fi-ta-spin 0.8s linear infinite; color:#f59e0b;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle style="opacity:.25;" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path style="opacity:.85;" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span style="font-size:13px; font-weight:500; color:#f3f4f6; letter-spacing:.01em;">Đang tải...</span>
            </div>
        </div>
    </div>

    <style>@keyframes fi-ta-spin { to { transform: rotate(360deg); } }</style>
</div>
