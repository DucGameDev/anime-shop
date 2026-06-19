<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\OrderItem;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Khách hàng';

    protected static ?string $modelLabel = 'Khách hàng';

    protected static ?string $pluralModelLabel = 'Khách hàng';

    protected static ?string $navigationGroup = 'Khách hàng';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('role', User::ROLE_CUSTOMER);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $_record): bool
    {
        return false;
    }

    public static function canDelete(Model $_record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Thông tin tài khoản')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Tên'),

                        TextEntry::make('email')
                            ->label('Email'),

                        TextEntry::make('created_at')
                            ->label('Ngày tham gia')
                            ->dateTime('d/m/Y H:i'),

                        TextEntry::make('orders_count')
                            ->label('Số đơn hàng')
                            ->state(fn (User $record): int => $record->orders()->count()),

                        TextEntry::make('orders_total')
                            ->label('Tổng chi tiêu')
                            ->state(fn (User $record): string =>
                                number_format((float) $record->orders()->sum('total_amount'), 0, ',', '.') . '₫'
                            ),
                    ])
                    ->columns(2),

                Section::make('Sản phẩm đã mua')
                    ->schema([
                        TextEntry::make('purchased_products')
                            ->label('')
                            ->columnSpanFull()
                            ->html()
                            ->state(function (User $record): string {
                                $orderIds = $record->orders()->pluck('id');

                                $groups = OrderItem::whereIn('order_id', $orderIds)
                                    ->with(['product' => fn ($q) => $q->withTrashed()])
                                    ->get()
                                    ->groupBy('product_id');

                                if ($groups->isEmpty()) {
                                    return '<p class="text-sm text-gray-500 dark:text-gray-400">Chưa mua sản phẩm nào.</p>';
                                }

                                $html = '<div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">';

                                foreach ($groups as $items) {
                                    $product = $items->first()->product;
                                    if ($product === null) {
                                        continue;
                                    }

                                    $url   = ProductResource::getUrl('edit', ['record' => $product->id]);
                                    $img   = e($product->image_url ?? '');
                                    $name  = e($product->name);
                                    $qty   = $items->sum('quantity');
                                    $total = number_format(
                                        $items->sum(fn ($i) => $i->quantity * (float) $i->price),
                                        0, ',', '.'
                                    ) . '₫';

                                    $html .= '<a href="' . $url . '" target="_blank"'
                                        . ' class="flex items-center gap-3 rounded-lg border border-gray-200 p-2 hover:border-primary transition-colors dark:border-white/10">';

                                    if ($img) {
                                        $html .= '<img src="' . $img . '" class="h-10 w-10 shrink-0 rounded-md object-cover" />';
                                    } else {
                                        $html .= '<div class="h-10 w-10 shrink-0 rounded-md bg-gray-100 dark:bg-white/5"></div>';
                                    }

                                    $html .= '<div class="min-w-0">'
                                        . '<p class="truncate text-sm font-medium text-gray-900 dark:text-white">' . $name . '</p>'
                                        . '<p class="text-xs text-gray-500 dark:text-gray-400">×' . $qty . ' — ' . $total . '</p>'
                                        . '</div></a>';
                                }

                                $html .= '</div>';

                                return $html;
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('orders_count')
                    ->label('Số đơn')
                    ->counts('orders')
                    ->sortable(),

                TextColumn::make('orders_sum_total_amount')
                    ->label('Tổng chi tiêu')
                    ->sum('orders', 'total_amount')
                    ->formatStateUsing(fn (mixed $state): string =>
                        number_format((float) ($state ?? 0), 0, ',', '.') . '₫'
                    )
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày tham gia')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('registered_from')
                    ->label('Đăng ký từ ngày')
                    ->form([
                        DatePicker::make('date')->label('Từ ngày'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder =>
                        $query->when(
                            $data['date'] ?? null,
                            fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '>=', $date)
                        )
                    )
                    ->indicateUsing(fn (array $data): ?string =>
                        ($data['date'] ?? null)
                            ? 'Từ ' . \Carbon\Carbon::parse($data['date'])->format('d/m/Y')
                            : null
                    ),

                Filter::make('registered_until')
                    ->label('Đăng ký đến ngày')
                    ->form([
                        DatePicker::make('date')->label('Đến ngày'),
                    ])
                    ->query(fn (Builder $query, array $data): Builder =>
                        $query->when(
                            $data['date'] ?? null,
                            fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '<=', $date)
                        )
                    )
                    ->indicateUsing(fn (array $data): ?string =>
                        ($data['date'] ?? null)
                            ? 'Đến ' . \Carbon\Carbon::parse($data['date'])->format('d/m/Y')
                            : null
                    ),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'view'  => Pages\ViewUser::route('/{record}'),
        ];
    }
}
