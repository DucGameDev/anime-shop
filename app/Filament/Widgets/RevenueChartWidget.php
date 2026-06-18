<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Doanh thu & đơn hàng 14 ngày gần nhất';

    protected static string $color = 'info';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $labels  = $days->map(fn ($d) => $d->format('d/m'))->toArray();
        $revenue = $days->map(fn ($d) => (float) Order::whereDate('created_at', $d)->sum('total_amount'))->toArray();
        $orders  = $days->map(fn ($d) => Order::whereDate('created_at', $d)->count())->toArray();

        return [
            'datasets' => [
                [
                    'label'           => 'Doanh thu (₫)',
                    'data'            => $revenue,
                    'fill'            => true,
                    'backgroundColor' => 'rgba(168, 85, 247, 0.08)',
                    'borderColor'     => '#A855F7',
                    'tension'         => 0.4,
                    'yAxisID'         => 'y',
                ],
                [
                    'label'           => 'Số đơn',
                    'data'            => $orders,
                    'fill'            => false,
                    'backgroundColor' => 'rgba(236, 72, 153, 0.8)',
                    'borderColor'     => '#EC4899',
                    'tension'         => 0.4,
                    'yAxisID'         => 'y1',
                    'type'            => 'bar',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'position' => 'left',
                    'ticks'    => [
                        'callback' => "function(v){ return new Intl.NumberFormat('vi-VN').format(v) + '₫'; }",
                    ],
                ],
                'y1' => [
                    'position' => 'right',
                    'grid'     => ['drawOnChartArea' => false],
                    'ticks'    => ['stepSize' => 1],
                ],
            ],
        ];
    }
}
