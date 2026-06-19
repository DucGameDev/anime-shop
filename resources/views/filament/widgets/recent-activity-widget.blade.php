<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Hoạt động gần đây</x-slot>
        <x-slot name="description">Sự kiện trong 7 ngày qua</x-slot>

        @if ($activities->isEmpty())
            <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Chưa có hoạt động nào.
            </div>
        @else
            <div class="divide-y divide-gray-100 dark:divide-white/5">
                @foreach ($activities as $activity)
                    <div class="flex items-center gap-3 py-3 first:pt-0 last:pb-0">

                        <div @class([
                            'flex h-9 w-9 shrink-0 items-center justify-center rounded-full',
                            'bg-amber-100 dark:bg-amber-900/40' => $activity['color'] === 'warning',
                            'bg-blue-100 dark:bg-blue-900/40'   => $activity['color'] === 'info',
                            'bg-green-100 dark:bg-green-900/40' => $activity['color'] === 'success',
                            'bg-red-100 dark:bg-red-900/40'     => $activity['color'] === 'danger',
                            'bg-gray-100 dark:bg-white/5'       => $activity['color'] === 'gray',
                        ])>
                            <x-filament::icon
                                :icon="$activity['icon']"
                                @class([
                                    'h-4 w-4',
                                    'text-amber-600 dark:text-amber-400' => $activity['color'] === 'warning',
                                    'text-blue-600 dark:text-blue-400'   => $activity['color'] === 'info',
                                    'text-green-600 dark:text-green-400' => $activity['color'] === 'success',
                                    'text-red-600 dark:text-red-400'     => $activity['color'] === 'danger',
                                    'text-gray-500 dark:text-gray-400'   => $activity['color'] === 'gray',
                                ])
                            />
                        </div>

                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $activity['title'] }}
                            </p>
                            <p class="truncate text-xs text-gray-500 dark:text-gray-400">
                                {{ $activity['desc'] }}
                            </p>
                        </div>

                        <time class="shrink-0 text-xs text-gray-400 dark:text-gray-500">
                            {{ $activity['time']->diffForHumans() }}
                        </time>

                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
