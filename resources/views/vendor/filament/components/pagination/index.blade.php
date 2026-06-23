@props([
    'currentPageOptionProperty' => 'tableRecordsPerPage',
    'extremeLinks' => false,
    'paginator',
    'pageOptions' => [],
])

@php
    use Illuminate\Pagination\UrlWindow;

    $isSimple = ! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;

    if (! $isSimple) {
        $window   = UrlWindow::make($paginator);
        $elements = array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    }
@endphp

<div
    {{
        $attributes->class([
            'fi-pagination flex flex-wrap items-center justify-between gap-2 py-1',
        ])
    }}
>
    {{-- Left: overview --}}
    @if (! $isSimple)
        <span class="text-sm text-gray-500 dark:text-gray-400 shrink-0">
            {{
                trans_choice(
                    'filament::components/pagination.overview',
                    $paginator->total(),
                    [
                        'first' => \Illuminate\Support\Number::format($paginator->firstItem() ?? 0),
                        'last'  => \Illuminate\Support\Number::format($paginator->lastItem() ?? 0),
                        'total' => \Illuminate\Support\Number::format($paginator->total()),
                    ],
                )
            }}
        </span>
    @endif

    {{-- Center: page numbers --}}
    @if (! $isSimple && $paginator->hasPages())
        <ol class="flex items-center gap-1">

            {{-- Prev --}}
            @if ($paginator->onFirstPage())
                <li><span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </span></li>
            @else
                <li><button
                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 dark:text-gray-400 bg-white dark:bg-white/5 ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10 transition-colors"
                >
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                </button></li>
            @endif

            {{-- Page numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 dark:text-gray-500">…</span></li>
                @elseif (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page === $paginator->currentPage())
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-semibold bg-primary-600 text-white">{{ $page }}</span>
                            @else
                                <button
                                    wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-white/5 ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10 transition-colors"
                                >{{ $page }}</button>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li><button
                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-600 dark:text-gray-400 bg-white dark:bg-white/5 ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10 transition-colors"
                >
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                </button></li>
            @else
                <li><span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-300 dark:text-gray-600 cursor-not-allowed">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                </span></li>
            @endif

        </ol>
    @elseif ($isSimple)
        <div class="flex items-center gap-1">
            @if (! $paginator->onFirstPage())
                <button wire:click="previousPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1 px-3 h-8 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-white/5 ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10 transition-colors">
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    {{ __('filament::components/pagination.actions.previous.label') }}
                </button>
            @endif
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage('{{ $paginator->getPageName() }}')" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1 px-3 h-8 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-white/5 ring-1 ring-gray-950/10 dark:ring-white/20 hover:bg-gray-50 dark:hover:bg-white/10 transition-colors">
                    {{ __('filament::components/pagination.actions.next.label') }}
                    <svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                </button>
            @endif
        </div>
    @endif

    {{-- Right: records per page --}}
    @if (count($pageOptions) > 1)
        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 shrink-0">
            <span class="hidden sm:inline">{{ __('filament::components/pagination.fields.records_per_page.label') }}</span>
            <x-filament::input.wrapper class="w-20">
                <x-filament::input.select :wire:model.live="$currentPageOptionProperty">
                    @foreach ($pageOptions as $option)
                        <option value="{{ $option }}">
                            {{ $option === 'all' ? __('filament::components/pagination.fields.records_per_page.options.all') : $option }}
                        </option>
                    @endforeach
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </label>
    @endif
</div>
