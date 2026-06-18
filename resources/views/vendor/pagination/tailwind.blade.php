@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Phân trang" class="flex flex-col sm:flex-row items-center justify-center gap-3">

        {{-- Thông tin kết quả --}}
        <p class="text-sm text-neutral-muted order-2 sm:order-1">
            Hiển thị
            <span class="font-semibold text-neutral-text">{{ $paginator->firstItem() }}</span>
            –
            <span class="font-semibold text-neutral-text">{{ $paginator->lastItem() }}</span>
            trong
            <span class="font-semibold text-neutral-text">{{ $paginator->total() }}</span>
            sản phẩm
        </p>

        {{-- Các nút trang --}}
        <div class="flex items-center gap-1 order-1 sm:order-2">

            {{-- Nút Trước --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-neutral-muted cursor-not-allowed bg-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-neutral-text bg-white hover:bg-primary-light hover:border-primary hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @endif

            {{-- Số trang --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="inline-flex items-center justify-center w-9 h-9 text-sm text-neutral-muted">…</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-semibold bg-primary text-white shadow-sm">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $url }}"
                               class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-sm font-medium text-neutral-text bg-white hover:bg-primary-light hover:border-primary hover:text-primary transition-colors">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Nút Sau --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-neutral-text bg-white hover:bg-primary-light hover:border-primary hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            @else
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 text-neutral-muted cursor-not-allowed bg-white">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </span>
            @endif

        </div>
    </nav>
@endif
