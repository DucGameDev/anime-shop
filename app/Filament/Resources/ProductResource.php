<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
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

                FileUpload::make('image_url')
                    ->label('Ảnh sản phẩm')
                    ->image()
                    ->disk(app()->environment('production') ? 's3' : 'public')
                    ->directory('products')
                    ->visibility('public')
                    ->imagePreviewHeight('120')
                    ->nullable()
                    ->formatStateUsing(function (?string $state, $record): ?string {
                        // Trả về raw DB value (path) để FileUpload render preview đúng
                        // getRawOriginal() tránh gọi qua accessor (accessor trả về full URL)
                        return $record?->getRawOriginal('image_url');
                    })
                    ->columnSpan(2),

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

                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Danh mục')
                    ->relationship('category', 'name'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
}
