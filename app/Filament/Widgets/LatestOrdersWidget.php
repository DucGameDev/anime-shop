<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrdersWidget extends BaseWidget
{
    protected static ?string $heading = 'Đơn hàng mới nhất';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->with('user')->latest()->limit(5))
            ->columns([
                TextColumn::make('id')
                    ->label('Mã đơn')
                    ->formatStateUsing(fn ($state) => '#' . str_pad((string) $state, 6, '0', STR_PAD_LEFT))
                    ->weight('bold'),

                TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->description(fn (Order $record): string => $record->user !== null
                        ? '🔵 ' . $record->customer_email
                        : '⚪ Khách vãng lai'
                    ),

                TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->formatStateUsing(fn ($state) => number_format((float) $state, 0, ',', '.') . '₫')
                    ->weight('medium'),

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
                    ->label('Thời gian')
                    ->dateTime('H:i d/m/Y')
                    ->sortable(),
            ])
            ->recordUrl(fn (Order $record): string => OrderResource::getUrl('edit', ['record' => $record]))
            ->paginated(false);
    }
}
