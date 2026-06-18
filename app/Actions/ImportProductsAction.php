<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;
use OpenSpout\Reader\XLSX\Reader;

class ImportProductsAction
{
    /** @return array{created: int, error_count: int, errors: string[]} */
    public function execute(string $filePath): array
    {
        $categories = Category::pluck('id', 'name')->toArray();
        $created    = 0;
        $errors     = [];
        $rowIndex   = 0;

        $reader = new Reader();
        $reader->open($filePath);

        foreach ($reader->getSheetIterator() as $sheet) {
            foreach ($sheet->getRowIterator() as $row) {
                $rowIndex++;

                if ($rowIndex === 1) {
                    continue; // bỏ dòng header
                }

                $cells  = $row->getCells();
                $values = array_map(fn ($cell): string => trim((string) $cell->getValue()), $cells);

                while (count($values) < 6) {
                    $values[] = '';
                }

                [$name, $price, $categoryName, $stock, $description, $imageUrl] = $values;

                if ($name === '') {
                    continue; // bỏ dòng trống
                }

                $rowErrors = $this->validate($rowIndex, $name, $price, $categoryName, $stock, $categories);

                if ($rowErrors !== []) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                Product::create([
                    'name'        => $name,
                    'slug'        => $this->uniqueSlug($name),
                    'price'       => (float) $price,
                    'category_id' => $categories[$categoryName],
                    'stock'       => (int) $stock,
                    'description' => $description !== '' ? $description : null,
                    'image_url'   => $imageUrl !== '' ? $imageUrl : null,
                ]);

                $created++;
            }

            break; // chỉ đọc sheet đầu tiên
        }

        $reader->close();

        return [
            'created'     => $created,
            'error_count' => count($errors),
            'errors'      => $errors,
        ];
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /** @return string[] */
    private function validate(
        int $row,
        string $name,
        string $price,
        string $categoryName,
        string $stock,
        array $categories,
    ): array {
        $errors = [];

        if ($name === '') {
            $errors[] = "Dòng {$row}: Tên sản phẩm không được trống.";
        }

        if (! is_numeric($price) || (float) $price < 0) {
            $errors[] = "Dòng {$row}: Giá không hợp lệ — nhập số, ví dụ 450000.";
        }

        if (! array_key_exists($categoryName, $categories)) {
            $valid    = implode(', ', array_keys($categories));
            $errors[] = "Dòng {$row}: Danh mục '{$categoryName}' không tồn tại. Hợp lệ: {$valid}.";
        }

        if (! ctype_digit($stock) || (int) $stock < 0) {
            $errors[] = "Dòng {$row}: Tồn kho phải là số nguyên không âm.";
        }

        return $errors;
    }
}
