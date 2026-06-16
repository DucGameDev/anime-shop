<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?string $heading = '5 đơn hàng mới nhất';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->latest()->limit(5)
            )
            ->columns([
                TextColumn::make('id')
                    ->label('Mã đơn')
                    ->formatStateUsing(fn ($state) => '#' . str_pad((string) $state, 6, '0', STR_PAD_LEFT)),

                TextColumn::make('customer_name')
                    ->label('Khách hàng'),

                TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, ',', '.') . '₫'),

                TextColumn::make('status')
                    ->label('Trạng thái')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'shipped'   => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'   => 'Chờ xử lý',
                        'shipped'   => 'Đang giao',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                        default     => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Ngày đặt')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->paginated(false);
    }
}
