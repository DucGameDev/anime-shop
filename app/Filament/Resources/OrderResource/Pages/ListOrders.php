<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Xuất CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function (): StreamedResponse {
                    $filename = 'orders-' . now()->format('Y-m-d') . '.csv';

                    return response()->streamDownload(function (): void {
                        $handle = fopen('php://output', 'w');

                        if ($handle === false) {
                            return;
                        }

                        fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM cho Excel
                        fputcsv($handle, ['ID', 'Khách hàng', 'Email', 'SĐT', 'Địa chỉ', 'Tổng tiền', 'Trạng thái', 'Ngày đặt']);

                        Order::cursor()->each(function (Order $order) use ($handle): void {
                            fputcsv($handle, [
                                '#' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                                $order->customer_name,
                                $order->customer_email ?? '',
                                $order->phone,
                                $order->address,
                                $order->total_amount,
                                match ($order->status) {
                                    'pending'   => 'Chờ xử lý',
                                    'shipped'   => 'Đang giao',
                                    'completed' => 'Hoàn thành',
                                    'cancelled' => 'Đã hủy',
                                    default     => $order->status,
                                },
                                $order->created_at->format('d/m/Y H:i'),
                            ]);
                        });

                        fclose($handle);
                    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                }),
        ];
    }
}
