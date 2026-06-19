<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationLabel = 'Danh mục';

    protected static ?string $modelLabel = 'Danh mục';

    protected static ?string $pluralModelLabel = 'Danh mục';

    protected static ?string $navigationGroup = 'Cửa hàng';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Tên danh mục')
                    ->required()
                    ->maxLength(100)
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (string $state, callable $set): void {
                        $set('slug', Str::slug($state));
                    }),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(table: Category::class, column: 'slug', ignoreRecord: true)
                    ->maxLength(100)
                    ->helperText('Tự động tạo từ tên. Không nên thay đổi sau khi đã có sản phẩm.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tên danh mục')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('products_count')
                    ->label('Số sản phẩm')
                    ->counts('products')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Cập nhật')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                EditAction::make(),

                DeleteAction::make()
                    ->before(function (Category $record, DeleteAction $action): void {
                        if ($record->products()->exists()) {
                            Notification::make()
                                ->danger()
                                ->title('Không thể xóa')
                                ->body("Danh mục \"{$record->name}\" còn {$record->products()->count()} sản phẩm. Hãy chuyển hoặc xóa sản phẩm trước.")
                                ->persistent()
                                ->send();

                            $action->cancel();
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (\Illuminate\Database\Eloquent\Collection $records, DeleteBulkAction $action): void {
                            $blocked = $records->filter(fn (Category $cat) => $cat->products()->exists());
                            if ($blocked->isNotEmpty()) {
                                Notification::make()
                                    ->danger()
                                    ->title('Không thể xóa ' . $blocked->count() . ' danh mục')
                                    ->body('Các danh mục còn sản phẩm: ' . $blocked->pluck('name')->join(', '))
                                    ->persistent()
                                    ->send();

                                $action->cancel();
                            }
                        }),
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
