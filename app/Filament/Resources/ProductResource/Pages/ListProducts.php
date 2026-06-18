<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProductResource\Pages;

use App\Actions\ImportProductsAction;
use App\Filament\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Tải file mẫu')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('admin.products.import-template'))
                ->openUrlInNewTab(),

            Action::make('import')
                ->label('Nhập từ Excel')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->form([
                    FileUpload::make('file')
                        ->label('File Excel (.xlsx)')
                        ->required()
                        ->disk('local')
                        ->directory('imports')
                        ->acceptedFileTypes([
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->maxSize(5120),
                ])
                ->action(function (array $data): void {
                    $relativePath = is_array($data['file']) ? $data['file'][0] : $data['file'];
                    $absolutePath = Storage::disk('local')->path($relativePath);

                    $result = (new ImportProductsAction())->execute($absolutePath);

                    Storage::disk('local')->delete($relativePath);

                    if ($result['error_count'] > 0) {
                        $errorPreview = implode("\n", array_slice($result['errors'], 0, 5));
                        $more         = $result['error_count'] > 5
                            ? "\n... và " . ($result['error_count'] - 5) . ' lỗi khác.'
                            : '';

                        Notification::make()
                            ->title("Nhập {$result['created']} sản phẩm — {$result['error_count']} dòng lỗi")
                            ->body($errorPreview . $more)
                            ->warning()
                            ->persistent()
                            ->send();
                    } else {
                        Notification::make()
                            ->title("Nhập thành công {$result['created']} sản phẩm")
                            ->success()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Tất cả')
                ->badge(Product::count()),
        ];

        foreach (Category::orderBy('name')->get() as $category) {
            $count = Product::where('category_id', $category->id)->count();

            $tabs[$category->slug] = Tab::make($category->name)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('category_id', $category->id))
                ->badge($count);
        }

        return $tabs;
    }
}
