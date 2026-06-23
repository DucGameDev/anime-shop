<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Header: tiêu đề + period selector --}}
        <x-slot name="heading">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <span>Hoạt động gần đây</span>
                <div class="flex items-center gap-1">
                    @foreach ([7 => '7 ngày', 30 => '30 ngày', 90 => '90 ngày'] as $value => $label)
                        <button
                            wire:click="setPeriod({{ $value }})"
                            class="px-3 py-1 rounded-full text-xs font-medium transition-colors
                                {{ $period === $value
                                    ? 'bg-amber-500 text-white'
                                    : 'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-white/20' }}"
                        >{{ $label }}</button>
                    @endforeach
                </div>
            </div>
        </x-slot>
        <x-slot name="description">
            {{ $total }} sự kiện trong {{ $period }} ngày qua
        </x-slot>

        {{-- Activity list --}}
        @if ($activities->isEmpty())
            <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Chưa có hoạt động nào trong {{ $period }} ngày qua.
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach ($activities as $activity)
                    <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">
                        <span @class([
                            'inline-block h-2.5 w-2.5 shrink-0 rounded-full',
                            'bg-amber-400'  => $activity['color'] === 'warning',
                            'bg-blue-400'   => $activity['color'] === 'info',
                            'bg-green-500'  => $activity['color'] === 'success',
                            'bg-red-500'    => $activity['color'] === 'danger',
                            'bg-gray-400'   => $activity['color'] === 'gray',
                        ])></span>

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900 dark:text-white">
                                {{ $activity['title'] }}
                            </p>
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ $activity['desc'] }}
                            </p>
                        </div>

                        <time class="shrink-0 whitespace-nowrap text-xs text-gray-400 dark:text-gray-500">
                            {{ $activity['time']->diffForHumans() }}
                        </time>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if ($totalPages > 1)
                <div class="mt-3 flex items-center justify-between border-t border-gray-100 dark:border-white/5 pt-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        {{ ($page - 1) * 5 + 1 }}–{{ min($page * 5, $total) }} / {{ $total }}
                    </span>

                    <nav class="flex items-center gap-0.5">
                        {{-- Prev --}}
                        @if ($page <= 1)
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-300 dark:text-gray-600 cursor-not-allowed select-none">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        @else
                            <button wire:click="previousPage" wire:loading.attr="disabled"
                                class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </button>
                        @endif

                        {{-- Page numbers --}}
                        @php $prev = null; @endphp
                        @foreach ($window as $p)
                            @if ($prev !== null && $p - $prev > 1)
                                <span class="inline-flex h-7 w-5 items-center justify-center text-xs text-gray-400 dark:text-gray-500 select-none">·</span>
                            @endif
                            @if ($p === $page)
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-md bg-amber-500 text-xs font-semibold text-white shadow-sm">{{ $p }}</span>
                            @else
                                <button wire:click="gotoPage({{ $p }})" wire:loading.attr="disabled"
                                    class="inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">{{ $p }}</button>
                            @endif
                            @php $prev = $p; @endphp
                        @endforeach

                        {{-- Next --}}
                        @if ($page >= $totalPages)
                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-300 dark:text-gray-600 cursor-not-allowed select-none">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            </span>
                        @else
                            <button wire:click="nextPage({{ $totalPages }})" wire:loading.attr="disabled"
                                class="inline-flex h-7 w-7 items-center justify-center rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-white/10 transition-colors">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                            </button>
                        @endif
                    </nav>
                </div>
            @endif
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
