<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class RecentActivityWidget extends Widget
{
    protected static ?string $heading = 'Hoạt động gần đây';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.recent-activity-widget';

    protected function getViewData(): array
    {
        $activities = collect();

        // Đơn hàng mới
        Order::latest()->limit(5)->get()->each(function (Order $order) use ($activities): void {
            $activities->push([
                'type'  => 'order',
                'icon'  => 'heroicon-o-shopping-cart',
                'color' => 'warning',
                'title' => 'Đơn hàng mới #' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                'desc'  => $order->customer_name . ' — ' . number_format((float) $order->total_amount, 0, ',', '.') . '₫',
                'time'  => $order->created_at,
            ]);
        });

        // Khách hàng mới
        User::latest()->limit(5)->get()->each(function (User $user) use ($activities): void {
            $activities->push([
                'type'  => 'user',
                'icon'  => 'heroicon-o-user-plus',
                'color' => 'success',
                'title' => 'Khách hàng mới',
                'desc'  => $user->name . ' — ' . $user->email,
                'time'  => $user->created_at,
            ]);
        });

        // Đơn hàng cập nhật trạng thái (không phải pending)
        Order::whereIn('status', ['shipped', 'completed', 'cancelled'])
            ->latest('updated_at')
            ->limit(5)
            ->get()
            ->each(function (Order $order) use ($activities): void {
                $activities->push([
                    'type'  => 'status',
                    'icon'  => 'heroicon-o-arrow-path',
                    'color' => match ($order->status) {
                        'shipped'   => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    },
                    'title' => 'Đơn #' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) . ' → ' . match ($order->status) {
                        'shipped'   => 'Đang giao',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default     => $order->status,
                    },
                    'desc'  => $order->customer_name,
                    'time'  => $order->updated_at,
                ]);
            });

        return [
            'activities' => $activities->sortByDesc('time')->take(15)->values(),
        ];
    }
}
