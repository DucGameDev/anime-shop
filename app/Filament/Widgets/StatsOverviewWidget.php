<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();

        return [
            Stat::make('Đơn hàng hôm nay', Order::whereDate('created_at', $today)->count())
                ->description('Tổng đơn đặt trong ngày')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),

            Stat::make('Doanh thu hôm nay', number_format(
                Order::whereDate('created_at', $today)->sum('total_amount'),
                0,
                ',',
                '.'
            ) . '₫')
                ->description('Từ các đơn hôm nay')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Đơn đang xử lý', Order::where('status', 'pending')->count())
                ->description('Chưa được xử lý')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Sắp hết hàng', Product::where('stock', '<', 5)->count())
                ->description('Sản phẩm còn dưới 5 cái')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),
        ];
    }
}
