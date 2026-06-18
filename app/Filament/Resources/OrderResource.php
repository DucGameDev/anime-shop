<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers\OrderItemsRelationManager;
use App\Models\Order;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Illuminate\Support\HtmlString;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Đơn hàng';

    protected static ?string $modelLabel = 'Đơn hàng';

    protected static ?string $pluralModelLabel = 'Đơn hàng';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('customer_name')
                    ->label('Khách hàng')
                    ->disabled(),

                TextInput::make('phone')
                    ->label('Điện thoại')
                    ->disabled(),

                TextInput::make('total_amount')
                    ->label('Tổng tiền')
                    ->disabled()
                    ->formatStateUsing(fn (mixed $state): string => number_format((float) $state, 0, ',', '.') . '₫'),

                TextInput::make('created_at')
                    ->label('Thời gian đặt')
                    ->disabled()
                    ->formatStateUsing(fn (mixed $state): string => \Carbon\Carbon::parse($state)->format('H:i d/m/Y')),

                Placeholder::make('status_progress')
                    ->label('Tiến trình đơn hàng')
                    ->columnSpanFull()
                    ->content(function (Get $get): HtmlString {
                        $status = $get('status') ?? 'pending';
                        $isCancelled = $status === 'cancelled';

                        $steps = [
                            ['key' => 'pending',   'label' => 'Chờ xử lý', 'icon' => '🕐'],
                            ['key' => 'shipped',   'label' => 'Đang giao',  'icon' => '🚚'],
                            ['key' => 'completed', 'label' => 'Hoàn thành', 'icon' => '✅'],
                        ];

                        $order = ['pending' => 0, 'shipped' => 1, 'completed' => 2];
                        $currentIdx = $order[$status] ?? 0;

                        $activeColor = match ($status) {
                            'shipped'   => '#3b82f6',
                            'completed' => '#10b981',
                            default     => '#f59e0b',
                        };

                        if ($isCancelled) {
                            return new HtmlString(
                                '<div style="display:flex;align-items:center;gap:0.75rem;padding:0.875rem 1.25rem;background:#fef2f2;border:1px solid #fecaca;border-radius:0.75rem;">'
                                . '<span style="font-size:1.5rem;">❌</span>'
                                . '<div style="font-weight:600;color:#dc2626;font-size:0.9375rem;">Đơn hàng đã bị hủy</div>'
                                . '</div>'
                            );
                        }

                        $html = '<div style="display:flex;align-items:flex-start;width:100%;padding:0.5rem 0;">';

                        foreach ($steps as $i => $step) {
                            $isDone    = $i < $currentIdx;
                            $isCurrent = $i === $currentIdx;

                            $bg    = ($isDone || $isCurrent) ? $activeColor : '#e5e7eb';
                            $color = ($isDone || $isCurrent) ? '#ffffff' : '#9ca3af';
                            $labelColor = $isCurrent ? $activeColor : ($isDone ? '#374151' : '#9ca3af');
                            $weight = $isCurrent ? '700' : '400';
                            $icon = $isDone ? '✓' : $step['icon'];

                            $html .= '<div style="display:flex;flex-direction:column;align-items:center;flex-shrink:0;">'
                                . '<div style="width:2.75rem;height:2.75rem;border-radius:50%;background:' . $bg . ';display:flex;align-items:center;justify-content:center;font-size:1.1rem;color:' . $color . ';box-shadow:' . ($isCurrent ? '0 0 0 4px ' . $activeColor . '33' : 'none') . ';">' . $icon . '</div>'
                                . '<span style="font-size:0.75rem;margin-top:0.4rem;font-weight:' . $weight . ';color:' . $labelColor . ';white-space:nowrap;">' . $step['label'] . '</span>'
                                . '</div>';

                            if ($i < count($steps) - 1) {
                                $lineColor = $i < $currentIdx ? $activeColor : '#e5e7eb';
                                $html .= '<div style="flex:1;height:3px;background:' . $lineColor . ';margin:1.375rem 0.375rem 0;border-radius:2px;"></div>';
                            }
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    }),

                ToggleButtons::make('status')
                    ->label('Đổi trạng thái')
                    ->required()
                    ->inline()
                    ->live()
                    ->columnSpanFull()
                    ->options([
                        'pending'   => 'Chờ xử lý',
                        'shipped'   => 'Đang giao',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ])
                    ->colors([
                        'pending'   => 'warning',
                        'shipped'   => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    ])
                    ->icons([
                        'pending'   => 'heroicon-o-clock',
                        'shipped'   => 'heroicon-o-truck',
                        'completed' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                    ]),

                Textarea::make('address')
                    ->label('Địa chỉ')
                    ->disabled()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('user'))
            ->columns([
                TextColumn::make('id')
                    ->label('Mã đơn')
                    ->formatStateUsing(fn (mixed $state): string => '#' . str_pad((string) $state, 6, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Khách hàng')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Order $record): string => $record->user !== null
                        ? '🔵 Thành viên'
                        : '⚪ Khách vãng lai'
                    ),

                TextColumn::make('phone')
                    ->label('Điện thoại')
                    ->searchable(),

                TextColumn::make('total_amount')
                    ->label('Tổng tiền')
                    ->formatStateUsing(fn (mixed $state): string => number_format((float) $state, 0, ',', '.') . '₫')
                    ->sortable(),

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
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Trạng thái')
                    ->options([
                        'pending'   => 'Chờ xử lý',
                        'shipped'   => 'Đang giao',
                        'completed' => 'Hoàn thành',
                        'cancelled' => 'Đã hủy',
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit'  => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
