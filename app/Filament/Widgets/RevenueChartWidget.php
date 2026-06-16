<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Doanh thu 7 ngày gần nhất';

    protected static string $color = 'info';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $labels = $days->map(fn ($d) => $d->format('d/m'))->toArray();

        $data = $days->map(fn ($d) => (float) Order::whereDate('created_at', $d)->sum('total_amount'))->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Doanh thu (₫)',
                    'data'            => $data,
                    'fill'            => true,
                    'backgroundColor' => 'rgba(168, 85, 247, 0.1)',
                    'borderColor'     => '#A855F7',
                    'tension'         => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
