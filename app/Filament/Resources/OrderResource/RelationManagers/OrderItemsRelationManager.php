<?php

declare(strict_types=1);

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Sản phẩm trong đơn';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('product.name')
                    ->label('Sản phẩm')
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label('Số lượng'),

                TextColumn::make('price')
                    ->label('Đơn giá')
                    ->formatStateUsing(fn (mixed $state): string =>
                        number_format((float) $state, 0, ',', '.') . '₫'
                    ),

                TextColumn::make('subtotal')
                    ->label('Thành tiền')
                    ->getStateUsing(fn (mixed $record): string =>
                        number_format((float) $record->quantity * (float) $record->price, 0, ',', '.') . '₫'
                    ),
            ])
            ->paginated(false);
    }
}
