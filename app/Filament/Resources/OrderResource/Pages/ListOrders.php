<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    public function getTabs(): array
    {
        $counts = Order::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $all = $counts->sum();

        return [
            'all' => Tab::make('Tất cả')
                ->badge($all ?: null),

            'unpaid' => Tab::make('Chưa thanh toán')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'unpaid'))
                ->badge($counts['unpaid'] ?? null)
                ->badgeColor('gray'),

            'pending' => Tab::make('Chờ xử lý')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge($counts['pending'] ?? null)
                ->badgeColor('warning'),

            'shipped' => Tab::make('Đang giao')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'shipped'))
                ->badge($counts['shipped'] ?? null)
                ->badgeColor('info'),

            'completed' => Tab::make('Hoàn thành')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'completed'))
                ->badge($counts['completed'] ?? null)
                ->badgeColor('success'),

            'cancelled' => Tab::make('Đã hủy')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'cancelled'))
                ->badge($counts['cancelled'] ?? null)
                ->badgeColor('danger'),
        ];
    }

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
