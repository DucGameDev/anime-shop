<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers\OrdersRelationManager;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Tài khoản';

    protected static ?string $modelLabel = 'Tài khoản';

    protected static ?string $pluralModelLabel = 'Tài khoản';

    protected static ?int $navigationSort = 4;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('role')
                    ->label('Vai trò')
                    ->options([
                        User::ROLE_ADMIN    => 'Admin',
                        User::ROLE_CUSTOMER => 'Khách hàng',
                    ])
                    ->required(),
            ]);
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

                        TextEntry::make('role')
                            ->label('Vai trò')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                User::ROLE_ADMIN    => 'warning',
                                User::ROLE_CUSTOMER => 'success',
                                default             => 'gray',
                            })
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                User::ROLE_ADMIN    => 'Admin',
                                User::ROLE_CUSTOMER => 'Khách hàng',
                                default             => $state,
                            }),

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

                TextColumn::make('role')
                    ->label('Vai trò')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN    => 'warning',
                        User::ROLE_CUSTOMER => 'success',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        User::ROLE_ADMIN    => 'Admin',
                        User::ROLE_CUSTOMER => 'Khách hàng',
                        default             => $state,
                    })
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
                SelectFilter::make('role')
                    ->label('Vai trò')
                    ->options([
                        User::ROLE_ADMIN    => 'Admin',
                        User::ROLE_CUSTOMER => 'Khách hàng',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()->label('Đổi role'),
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
            'edit'  => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
