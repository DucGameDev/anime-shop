<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Sản phẩm';

    protected static ?string $modelLabel = 'Sản phẩm';

    protected static ?string $pluralModelLabel = 'Sản phẩm';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Tên sản phẩm')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, callable $set): void {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(table: Product::class, column: 'slug', ignoreRecord: true)
                    ->maxLength(255),

                TextInput::make('price')
                    ->label('Giá')
                    ->numeric()
                    ->prefix('₫')
                    ->required()
                    ->minValue(0),

                Select::make('category_id')
                    ->label('Danh mục')
                    ->required()
                    ->relationship('category', 'name')
                    ->preload(),

                TextInput::make('stock')
                    ->label('Tồn kho')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->required()
                    ->default(0),

                TextInput::make('image_url')
                    ->label('URL ảnh')
                    ->url()
                    ->maxLength(2048)
                    ->placeholder('https://example.com/image.jpg')
                    ->columnSpanFull(),

                RichEditor::make('description')
                    ->label('Mô tả')
                    ->nullable()
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Ảnh')
                    ->size(48)
                    ->disk(app()->environment('production') ? 's3' : 'public')
                    ->checkFileExistence(false),

                TextColumn::make('name')
                    ->label('Tên sản phẩm')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('category.name')
                    ->label('Danh mục')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Figure'  => 'purple',
                        'Áo'      => 'pink',
                        'Manga'   => 'info',
                        'Sticker' => 'warning',
                        default   => 'gray',
                    }),

                TextColumn::make('price')
                    ->label('Giá')
                    ->sortable()
                    ->formatStateUsing(fn (mixed $state): string => number_format((float) $state, 0, ',', '.') . '₫'),

                TextColumn::make('stock')
                    ->label('Tồn kho')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Ngày thêm')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('category')
                    ->label('Danh mục')
                    ->relationship('category', 'name'),

                Filter::make('created_at')
                    ->label('Ngày thêm')
                    ->form([
                        DatePicker::make('from')->label('Từ ngày'),
                        DatePicker::make('until')->label('Đến ngày'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, string $date): Builder => $q->whereDate('created_at', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) {
                            $indicators[] = 'Từ ' . \Carbon\Carbon::parse($data['from'])->format('d/m/Y');
                        }
                        if ($data['until'] ?? null) {
                            $indicators[] = 'Đến ' . \Carbon\Carbon::parse($data['until'])->format('d/m/Y');
                        }
                        return $indicators;
                    }),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('changeCategory')
                        ->label('Đổi danh mục')
                        ->icon('heroicon-o-tag')
                        ->color('warning')
                        ->form([
                            Select::make('category_id')
                                ->label('Danh mục mới')
                                ->required()
                                ->relationship('category', 'name')
                                ->preload(),
                        ])
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records, array $data): void {
                            $records->each->update(['category_id' => $data['category_id']]);
                        })
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
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
            'index'  => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit'   => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(SoftDeletingScope::class);
    }
}
