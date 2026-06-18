<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();
        $monthStart = Carbon::now()->startOfMonth();

        // Sparkline data: 7 ngày gần nhất
        $last7 = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $orderSparkline   = $last7->map(fn ($d) => Order::whereDate('created_at', $d)->count())->toArray();
        $revenueSparkline = $last7->map(fn ($d) => (float) Order::whereDate('created_at', $d)->sum('total_amount'))->toArray();

        // Hôm nay
        $ordersToday        = Order::whereDate('created_at', $today)->count();
        $revenueToday       = (float) Order::whereDate('created_at', $today)->sum('total_amount');

        // Hôm qua (để so sánh)
        $ordersYesterday    = Order::whereDate('created_at', $yesterday)->count();
        $revenueYesterday   = (float) Order::whereDate('created_at', $yesterday)->sum('total_amount');

        // Tháng này
        $ordersMonth        = Order::where('created_at', '>=', $monthStart)->count();
        $revenueMonth       = (float) Order::where('created_at', '>=', $monthStart)->sum('total_amount');

        // Trend helpers
        $orderDiff   = $ordersToday - $ordersYesterday;
        $revenueDiff = $revenueToday - $revenueYesterday;

        $orderTrendDesc   = $orderDiff >= 0
            ? '+' . $orderDiff . ' so với hôm qua'
            : $orderDiff . ' so với hôm qua';
        $orderTrendColor  = $orderDiff >= 0 ? 'success' : 'danger';
        $orderTrendIcon   = $orderDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        $revenueDiffFmt   = number_format(abs($revenueDiff), 0, ',', '.') . '₫';
        $revenueTrendDesc = $revenueDiff >= 0
            ? '+' . $revenueDiffFmt . ' so với hôm qua'
            : '-' . $revenueDiffFmt . ' so với hôm qua';
        $revenueTrendColor = $revenueDiff >= 0 ? 'success' : 'danger';
        $revenueTrendIcon  = $revenueDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        return [
            Stat::make('Đơn hàng hôm nay', $ordersToday)
                ->description($orderTrendDesc)
                ->descriptionIcon($orderTrendIcon)
                ->descriptionColor($orderTrendColor)
                ->chart($orderSparkline)
                ->color('info'),

            Stat::make('Doanh thu hôm nay', number_format($revenueToday, 0, ',', '.') . '₫')
                ->description($revenueTrendDesc)
                ->descriptionIcon($revenueTrendIcon)
                ->descriptionColor($revenueTrendColor)
                ->chart($revenueSparkline)
                ->color('success'),

            Stat::make('Đơn tháng ' . Carbon::now()->format('m'), $ordersMonth)
                ->description('Tổng đơn trong tháng')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),

            Stat::make('Doanh thu tháng ' . Carbon::now()->format('m'), number_format($revenueMonth, 0, ',', '.') . '₫')
                ->description('Tổng doanh thu trong tháng')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Đơn đang xử lý', Order::where('status', 'pending')->count())
                ->description('Chưa được xử lý')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Khách hàng', User::where('role', 'customer')->count())
                ->description('Tài khoản đã đăng ký')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Sắp hết hàng', Product::where('stock', '<', 5)->count())
                ->description('Sản phẩm còn dưới 5 cái')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Hết hàng', Product::where('stock', 0)->count())
                ->description('Sản phẩm cần nhập thêm')
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color('gray'),

            Stat::make('Tổng sản phẩm', Product::count())
                ->description('Đang bán trong cửa hàng')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
        ];
    }
}
