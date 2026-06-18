<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Đơn hàng theo trạng thái';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        $statuses = [
            'pending'   => ['label' => 'Chờ xử lý', 'color' => '#F59E0B'],
            'shipped'   => ['label' => 'Đang giao',  'color' => '#3B82F6'],
            'completed' => ['label' => 'Hoàn thành', 'color' => '#10B981'],
            'cancelled' => ['label' => 'Đã hủy',     'color' => '#EF4444'],
        ];

        $counts = Order::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'datasets' => [
                [
                    'data'            => collect($statuses)->keys()->map(fn ($k) => $counts[$k] ?? 0)->toArray(),
                    'backgroundColor' => collect($statuses)->pluck('color')->toArray(),
                    'borderWidth'     => 2,
                ],
            ],
            'labels' => collect($statuses)->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
