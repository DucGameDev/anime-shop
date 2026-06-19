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
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
