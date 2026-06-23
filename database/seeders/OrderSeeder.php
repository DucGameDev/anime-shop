<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::inRandomOrder()->limit(10)->get();
        if ($products->isEmpty()) {
            return;
        }

        $customers = [
            ['name' => 'Trần Thị Bình', 'email' => 'binh.tran@gmail.com',  'phone' => '0901234567'],
            ['name' => 'Lê Văn Cường',  'email' => 'cuong.le@yahoo.com',   'phone' => '0912345678'],
            ['name' => 'Nguyễn Mai Anh', 'email' => 'maianh@hotmail.com',  'phone' => '0923456789'],
            ['name' => 'Phạm Quốc Hùng', 'email' => 'hung.pham@gmail.com', 'phone' => '0934567890'],
            ['name' => 'Võ Thị Lan',    'email' => 'lan.vo@gmail.com',     'phone' => '0945678901'],
            ['name' => 'Hoàng Minh Tuấn','email' => null,                  'phone' => '0956789012'],
            ['name' => 'Đặng Thị Hoa',  'email' => 'hoa.dang@gmail.com',  'phone' => '0967890123'],
            ['name' => 'Bùi Văn Nam',   'email' => null,                   'phone' => '0978901234'],
        ];

        $member = User::where('role', 'customer')->first();

        $statuses = ['unpaid', 'pending', 'shipped', 'shipped', 'completed', 'completed', 'cancelled'];

        foreach ($statuses as $i => $status) {
            $customer = $customers[$i % count($customers)];
            $product  = $products[$i % $products->count()];
            $qty      = rand(1, 3);
            $total    = (float) $product->price * $qty;

            $order = Order::create([
                'customer_name'   => $customer['name'],
                'customer_email'  => $customer['email'],
                'phone'           => $customer['phone'],
                'address'         => fake()->address(),
                'note'            => $i % 3 === 0 ? 'Giao giờ hành chính, gọi trước khi giao.' : null,
                'payment_method'  => $i % 2 === 0 ? 'bank_transfer' : 'cod',
                'voucher_code'    => null,
                'discount_amount' => 0,
                'status'          => $status,
                'total_amount'    => $total,
                'created_at'      => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
            ]);

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $qty,
                'price'      => $product->price,
            ]);
        }

        // 1 đơn liên kết thành viên
        if ($member) {
            $product = $products->first();
            $order = Order::create([
                'customer_name'   => $member->name,
                'customer_email'  => $member->email,
                'phone'           => '0987654321',
                'address'         => '123 Đường Lê Lợi, Quận 1, TP.HCM',
                'note'            => null,
                'payment_method'  => 'bank_transfer',
                'voucher_code'    => null,
                'discount_amount' => 0,
                'status'          => 'completed',
                'total_amount'    => (float) $product->price,
                'created_at'      => now()->subDays(2),
            ]);

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => 1,
                'price'      => $product->price,
            ]);
        }
    }
}
