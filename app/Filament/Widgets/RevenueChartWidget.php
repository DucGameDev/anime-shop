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

    protected int | string | array $columnSpan = 1;

    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        // 1 query thay vì 28
        $raw = Order::selectRaw('DATE(created_at) as date, SUM(total_amount) as rev, COUNT(*) as cnt')
            ->where('created_at', '>=', Carbon::today()->subDays(13)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->keyBy('date');

        $labels  = $days->map(fn ($d) => $d->format('d/m'))->toArray();
        $revenue = $days->map(fn ($d) => (float) ($raw[$d->format('Y-m-d')]->rev ?? 0))->toArray();
        $orders  = $days->map(fn ($d) => (int) ($raw[$d->format('Y-m-d')]->cnt ?? 0))->toArray();

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
