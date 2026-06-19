<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'orders:cancel-unpaid';

    protected $description = 'Hủy các đơn hàng chưa thanh toán quá 24 giờ';

    public function handle(): int
    {
        $cutoff = now()->subHours(24);

        $orders = Order::where('status', 'unpaid')
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('Không có đơn nào cần hủy.');
            return self::SUCCESS;
        }

        foreach ($orders as $order) {
            $order->update(['status' => 'cancelled']);
            Log::info('Auto-cancelled unpaid order', ['order_id' => $order->id]);
        }

        $this->info("Đã hủy {$orders->count()} đơn hàng chưa thanh toán.");

        return self::SUCCESS;
    }
}
