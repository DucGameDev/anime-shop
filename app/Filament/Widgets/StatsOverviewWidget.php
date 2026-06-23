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
        $today      = Carbon::today();
        $yesterday  = Carbon::yesterday();
        $monthStart = Carbon::now()->startOfMonth();

        // 1 query cho cả sparkline 7 ngày + hôm nay + hôm qua
        $sparklineRaw = Order::selectRaw('DATE(created_at) as date, COUNT(*) as cnt, SUM(total_amount) as rev')
            ->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay())
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->keyBy('date');

        $sparkDays = collect(range(6, 0))->map(fn ($i) => Carbon::today()->subDays($i));

        $orderSparkline   = $sparkDays->map(fn ($d) => (int) ($sparklineRaw[$d->format('Y-m-d')]->cnt ?? 0))->toArray();
        $revenueSparkline = $sparkDays->map(fn ($d) => (float) ($sparklineRaw[$d->format('Y-m-d')]->rev ?? 0))->toArray();

        $todayRow     = $sparklineRaw[$today->format('Y-m-d')]    ?? null;
        $yesterdayRow = $sparklineRaw[$yesterday->format('Y-m-d')] ?? null;

        $ordersToday      = (int) ($todayRow->cnt ?? 0);
        $revenueToday     = (float) ($todayRow->rev ?? 0);
        $ordersYesterday  = (int) ($yesterdayRow->cnt ?? 0);
        $revenueYesterday = (float) ($yesterdayRow->rev ?? 0);

        // 1 query cho tháng này
        $monthRow     = Order::where('created_at', '>=', $monthStart)
            ->selectRaw('COUNT(*) as cnt, COALESCE(SUM(total_amount), 0) as rev')
            ->first();
        $ordersMonth  = (int) ($monthRow->cnt ?? 0);
        $revenueMonth = (float) ($monthRow->rev ?? 0);

        // Trend so với hôm qua
        $orderDiff   = $ordersToday - $ordersYesterday;
        $revenueDiff = $revenueToday - $revenueYesterday;

        $orderTrendDesc  = ($orderDiff >= 0 ? '+' : '') . $orderDiff . ' so với hôm qua';
        $orderTrendColor = $orderDiff >= 0 ? 'success' : 'danger';
        $orderTrendIcon  = $orderDiff >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';

        $revenueTrendDesc  = ($revenueDiff >= 0 ? '+' : '-') . number_format(abs($revenueDiff), 0, ',', '.') . '₫ so với hôm qua';
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

            Stat::make('Sắp hết hàng', Product::whereBetween('stock', [1, 4])->count())
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
