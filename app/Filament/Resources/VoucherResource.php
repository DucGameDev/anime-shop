<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherResource\Pages;
use App\Models\Voucher;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rule;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Cửa hàng';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Mã giảm giá';

    protected static ?string $modelLabel = 'Mã giảm giá';

    protected static ?string $pluralModelLabel = 'Mã giảm giá';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Mã giảm giá')
                    ->placeholder('SUMMER20')
                    ->required()
                    ->maxLength(255)
                    ->extraInputAttributes(['style' => 'text-transform: uppercase'])
                    ->dehydrateStateUsing(fn (string $state): string => strtoupper($state))
                    ->unique(
                        table: 'vouchers',
                        column: 'code',
                        ignorable: fn ($record) => $record,
                    ),

                Forms\Components\ToggleButtons::make('type')
                    ->label('Loại giảm giá')
                    ->options([
                        'percent' => 'Phần trăm (%)',
                        'fixed'   => 'Số tiền cố định (₫)',
                    ])
                    ->inline()
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('value')
                    ->label('Giá trị giảm')
                    ->numeric()
                    ->required()
                    ->prefix(fn (Get $get): string => $get('type') === 'percent' ? '%' : '₫'),

                Forms\Components\TextInput::make('min_order')
                    ->label('Đơn tối thiểu')
                    ->numeric()
                    ->suffix('₫')
                    ->helperText('Bỏ trống nếu không giới hạn')
                    ->nullable(),

                Forms\Components\TextInput::make('max_uses')
                    ->label('Số lần dùng tối đa')
                    ->numeric()
                    ->helperText('Bỏ trống = không giới hạn')
                    ->nullable(),

                Forms\Components\DateTimePicker::make('expires_at')
                    ->label('Hết hạn lúc')
                    ->nullable(),

                Forms\Components\Toggle::make('is_active')
                    ->label('Đang kích hoạt')
                    ->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Mã')
                    ->searchable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('type')
                    ->label('Loại')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'percent' => 'Phần trăm',
                        'fixed'   => 'Cố định',
                        default   => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'percent' => 'info',
                        'fixed'   => 'warning',
                        default   => 'gray',
                    }),

                Tables\Columns\TextColumn::make('value')
                    ->label('Giá trị')
                    ->formatStateUsing(function (string $state, $record): string {
                        return $record->type === 'percent'
                            ? number_format((float) $state, 0) . '%'
                            : number_format((float) $state, 0) . '₫';
                    }),

                Tables\Columns\TextColumn::make('min_order')
                    ->label('Đơn tối thiểu')
                    ->formatStateUsing(fn ($state): string => $state !== null
                        ? number_format((float) $state, 0) . '₫'
                        : '—'
                    )
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('used_count')
                    ->label('Đã dùng')
                    ->formatStateUsing(function ($state, $record): string {
                        $max = $record->max_uses !== null
                            ? (string) $record->max_uses
                            : '∞';

                        return $state . '/' . $max;
                    }),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Hết hạn')
                    ->dateTime('d/m/Y H:i')
                    ->color(fn ($state): ?string => $state !== null && $state->isPast() ? 'danger' : null),

                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Kích hoạt'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ngày tạo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Loại giảm giá')
                    ->options([
                        'percent' => 'Phần trăm (%)',
                        'fixed'   => 'Số tiền cố định (₫)',
                    ]),

                Tables\Filters\SelectFilter::make('is_active')
                    ->label('Trạng thái')
                    ->options([
                        '1' => 'Đang hoạt động',
                        '0' => 'Đã tắt',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListVouchers::route('/'),
            'create' => Pages\CreateVoucher::route('/create'),
            'edit'   => Pages\EditVoucher::route('/{record}/edit'),
        ];
    }
}
