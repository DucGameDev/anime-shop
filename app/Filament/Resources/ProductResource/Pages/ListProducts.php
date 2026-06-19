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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Xuất CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function (): StreamedResponse {
                    $filename = 'products-' . now()->format('Y-m-d') . '.csv';

                    return response()->streamDownload(function (): void {
                        $handle = fopen('php://output', 'w');

                        if ($handle === false) {
                            return;
                        }

                        fputs($handle, "\xEF\xBB\xBF"); // UTF-8 BOM cho Excel
                        fputcsv($handle, ['ID', 'Tên sản phẩm', 'Danh mục', 'Giá', 'Tồn kho', 'Ngày thêm']);

                        Product::with('category')->cursor()->each(function (Product $product) use ($handle): void {
                            fputcsv($handle, [
                                $product->id,
                                $product->name,
                                $product->category?->name ?? '',
                                $product->price,
                                $product->stock,
                                $product->created_at->format('d/m/Y H:i'),
                            ]);
                        });

                        fclose($handle);
                    }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
                }),

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
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNull('deleted_at'))
                ->badge(Product::whereNull('deleted_at')->count()),
        ];

        foreach (Category::orderBy('name')->get() as $category) {
            $count = Product::whereNull('deleted_at')->where('category_id', $category->id)->count();

            $tabs[$category->slug] = Tab::make($category->name)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNull('deleted_at')->where('category_id', $category->id))
                ->badge($count);
        }

        $tabs['trashed'] = Tab::make('Đã xóa')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNotNull('deleted_at'))
            ->badge(Product::whereNotNull('deleted_at')->count())
            ->badgeColor('danger');

        return $tabs;
    }
}
