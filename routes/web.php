<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Writer\XLSX\Options;
use OpenSpout\Writer\XLSX\Writer;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/products', fn () => view('products.index'))->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/cart', fn () => view('cart.index'))->name('cart.index');
Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
Route::get('/checkout', fn () => view('checkout.index'))->name('checkout.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/products/import-template', function () {
        $tempPath   = sys_get_temp_dir() . '/product-template-' . uniqid() . '.xlsx';
        $categories = \App\Models\Category::pluck('name')->join(', ');

        $makeBorder = fn (string $color): Border => new Border(
            new BorderPart(Border::LEFT,   $color, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::RIGHT,  $color, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::TOP,    $color, Border::WIDTH_THIN, Border::STYLE_SOLID),
            new BorderPart(Border::BOTTOM, $color, Border::WIDTH_THIN, Border::STYLE_SOLID),
        );

        // Header: xanh đậm — chữ trắng — border xanh — căn giữa
        $headerStyle = (new Style())
            ->setFontBold()
            ->setFontSize(11)
            ->setFontColor('FFFFFFFF')
            ->setBackgroundColor('FF1E3A8A')
            ->setBorder($makeBorder('FF1E3A8A'))
            ->setCellAlignment(CellAlignment::CENTER);

        // Dòng ví dụ: xanh nhạt — chữ đậm nhẹ — border xám
        $exampleStyle = (new Style())
            ->setFontSize(11)
            ->setFontColor('FF1E3A8A')
            ->setBackgroundColor('FFDBEAFE')
            ->setBorder($makeBorder('FFB0C4DE'));

        // Dòng ghi chú: nền vàng nhạt — chữ xám — in nghiêng
        $noteStyle = (new Style())
            ->setFontItalic()
            ->setFontSize(10)
            ->setFontColor('FF64748B')
            ->setBackgroundColor('FFFFFBEB');

        $options = new Options();
        $options->setColumnWidth(35, 1); // Tên sản phẩm
        $options->setColumnWidth(15, 2); // Giá
        $options->setColumnWidth(18, 3); // Danh mục
        $options->setColumnWidth(12, 4); // Tồn kho
        $options->setColumnWidth(42, 5); // Mô tả
        $options->setColumnWidth(35, 6); // URL ảnh

        $writer = new Writer($options);
        $writer->openToFile($tempPath);

        $writer->addRow(Row::fromValues(
            ['Tên sản phẩm', 'Giá (₫)', 'Danh mục', 'Tồn kho', 'Mô tả', 'URL ảnh'],
            $headerStyle,
        ));

        $writer->addRow(Row::fromValues(
            ['Mô hình Naruto', 450000, 'Figure', 10, 'Mô hình cao 25cm chất liệu PVC', ''],
            $exampleStyle,
        ));

        $writer->addRow(Row::fromValues(
            ['⚠ Danh mục hợp lệ: ' . $categories, '', '', '', '', ''],
            $noteStyle,
        ));

        $writer->close();

        return response()->download($tempPath, 'san-pham-mau.xlsx')->deleteFileAfterSend(true);
    })->name('admin.products.import-template');
});

require __DIR__.'/auth.php';
