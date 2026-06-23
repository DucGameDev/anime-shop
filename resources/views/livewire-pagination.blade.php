<div class="mt-6 flex flex-wrap items-center justify-between gap-3">

    {{-- Summary --}}
    <p class="text-sm text-neutral-muted">
        Hiển thị
        <span class="font-semibold text-neutral-text">{{ $paginator->firstItem() }}</span>
        –
        <span class="font-semibold text-neutral-text">{{ $paginator->lastItem() }}</span>
        trong
        <span class="font-semibold text-neutral-text">{{ $paginator->total() }}</span>
        sản phẩm
    </p>

    {{-- Pagination buttons --}}
    @if ($paginator->hasPages())
    <nav role="navigation" aria-label="Phân trang" class="flex items-center gap-1">

        {{-- Prev --}}
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-neutral-muted/40 cursor-not-allowed bg-white select-none">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </span>
        @else
            <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-neutral-text bg-white hover:bg-primary-light hover:border-primary hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            </button>
        @endif

        {{-- Page numbers --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-neutral-muted select-none">…</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-semibold bg-primary text-white shadow-sm select-none">{{ $page }}</span>
                    @else
                        <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-sm font-medium text-neutral-text bg-white hover:bg-primary-light hover:border-primary hover:text-primary transition-colors">
                            {{ $page }}
                        </button>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-neutral-text bg-white hover:bg-primary-light hover:border-primary hover:text-primary transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </button>
        @else
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-neutral-muted/40 cursor-not-allowed bg-white select-none">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
            </span>
        @endif

    </nav>
    @endif

</div>
