<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Hoạt động gần đây
        </x-slot>

        <x-slot name="description">
            Sự kiện trong 7 ngày qua
        </x-slot>

        @if ($activities->isEmpty())
            <div class="py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                Chưa có hoạt động nào.
            </div>
        @else
            <ol class="relative border-s border-gray-200 dark:border-gray-700 ms-3">
                @foreach ($activities as $activity)
                    <li class="mb-6 ms-6 last:mb-0">
                        <span @class([
                            'absolute flex items-center justify-center w-8 h-8 rounded-full -start-4 ring-4 ring-white dark:ring-gray-900',
                            'bg-amber-100 dark:bg-amber-900'  => $activity['color'] === 'warning',
                            'bg-blue-100 dark:bg-blue-900'    => $activity['color'] === 'info',
                            'bg-green-100 dark:bg-green-900'  => $activity['color'] === 'success',
                            'bg-red-100 dark:bg-red-900'      => $activity['color'] === 'danger',
                            'bg-gray-100 dark:bg-gray-700'    => $activity['color'] === 'gray',
                        ])>
                            <x-filament::icon
                                :icon="$activity['icon']"
                                @class([
                                    'w-4 h-4',
                                    'text-amber-600 dark:text-amber-300'  => $activity['color'] === 'warning',
                                    'text-blue-600 dark:text-blue-300'    => $activity['color'] === 'info',
                                    'text-green-600 dark:text-green-300'  => $activity['color'] === 'success',
                                    'text-red-600 dark:text-red-300'      => $activity['color'] === 'danger',
                                    'text-gray-600 dark:text-gray-300'    => $activity['color'] === 'gray',
                                ])
                            />
                        </span>

                        <div class="p-3 bg-white border border-gray-200 rounded-lg shadow-xs dark:bg-gray-800 dark:border-gray-700">
                            <div class="flex items-center justify-between mb-0.5">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                    {{ $activity['title'] }}
                                </span>
                                <time class="text-xs font-normal text-gray-400 dark:text-gray-500 shrink-0 ms-3">
                                    {{ $activity['time']->diffForHumans() }}
                                </time>
                            </div>
                            <p class="text-xs font-normal text-gray-500 dark:text-gray-400 truncate">
                                {{ $activity['desc'] }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
