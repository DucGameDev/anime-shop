<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('unpaid','pending','shipped','completed','cancelled') NOT NULL DEFAULT 'unpaid'");
    }

    public function down(): void
    {
        DB::statement("UPDATE orders SET status = 'pending' WHERE status = 'unpaid'");
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending','shipped','completed','cancelled') NOT NULL DEFAULT 'pending'");
    }
};
