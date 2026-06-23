<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class RecentActivityWidget extends Widget
{
    protected static ?string $heading = 'Hoạt động gần đây';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    protected static string $view = 'filament.widgets.recent-activity-widget';

    public int $period = 7;

    public int $page = 1;

    protected int $perPage = 5;

    protected function queryString(): array
    {
        return [];
    }

    public function setPeriod(int $period): void
    {
        $this->period = $period;
        $this->page   = 1;
    }

    public function previousPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
        }
    }

    public function nextPage(int $totalPages): void
    {
        if ($this->page < $totalPages) {
            $this->page++;
        }
    }

    public function gotoPage(int $page): void
    {
        $this->page = $page;
    }

    protected function getViewData(): array
    {
        $startDate  = Carbon::now()->subDays($this->period)->startOfDay();
        $activities = collect();

        Order::where('created_at', '>=', $startDate)
            ->latest()
            ->get()
            ->each(function (Order $order) use ($activities): void {
                $activities->push([
                    'type'  => 'order',
                    'color' => 'warning',
                    'title' => 'Đơn hàng mới #' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                    'desc'  => $order->customer_name . ' — ' . number_format((float) $order->total_amount, 0, ',', '.') . '₫',
                    'time'  => $order->created_at,
                ]);
            });

        User::where('created_at', '>=', $startDate)
            ->latest()
            ->get()
            ->each(function (User $user) use ($activities): void {
                $activities->push([
                    'type'  => 'user',
                    'color' => 'success',
                    'title' => 'Khách hàng mới',
                    'desc'  => $user->name . ' — ' . $user->email,
                    'time'  => $user->created_at,
                ]);
            });

        Order::whereIn('status', ['shipped', 'completed', 'cancelled'])
            ->where('updated_at', '>=', $startDate)
            ->latest('updated_at')
            ->get()
            ->each(function (Order $order) use ($activities): void {
                $activities->push([
                    'type'  => 'status',
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

        $sorted     = $activities->sortByDesc('time')->values();
        $total      = $sorted->count();
        $totalPages = (int) ceil($total / $this->perPage);
        $page       = min($this->page, max(1, $totalPages));
        $items      = $sorted->slice(($page - 1) * $this->perPage, $this->perPage)->values();

        $window = collect(range(1, $totalPages))
            ->filter(fn ($p) => $p === 1 || $p === $totalPages || abs($p - $page) <= 2)
            ->values();

        return [
            'activities' => $items,
            'total'      => $total,
            'page'       => $page,
            'totalPages' => $totalPages,
            'window'     => $window,
            'period'     => $this->period,
        ];
    }
}
