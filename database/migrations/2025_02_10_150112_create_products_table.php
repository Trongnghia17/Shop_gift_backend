<?php

use Illuminate\Database\Migrations\Migration; // Sử dụng lớp Migration để định nghĩa một file migration.
use Illuminate\Database\Schema\Blueprint;     // Sử dụng Blueprint để định nghĩa cấu trúc bảng.
use Illuminate\Support\Facades\Schema;       // Sử dụng Schema để thao tác với cơ sở dữ liệu.

return new class extends Migration // Tạo một class ẩn danh kế thừa từ Migration.
{
    /**
     * Run the migrations.
     */
    public function up(): void // Hàm này được chạy khi thực thi migration (php artisan migrate).
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')
            ->references('id')
            ->on('categories')
            ->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name');
            $table->mediumText('description')->nullable();
            $table->string('brand');
            $table->decimal('selling_price', 15, 0);
            $table->decimal('original_price', 15, 0);
            $table->decimal('quantity', 15, 0);
            $table->string('image')->nullable();
            $table->tinyInteger('featured')->default('0')->nullable();
            $table->tinyInteger('popular')->default('0')->nullable();
            $table->tinyInteger('status')->default('1');
            $table->integer('count')->default('0')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void // Hàm này chạy khi rollback migration (php artisan migrate:rollback).
    {
        Schema::dropIfExists('products');
        // Xóa bảng 'products' nếu tồn tại.
    }
};
