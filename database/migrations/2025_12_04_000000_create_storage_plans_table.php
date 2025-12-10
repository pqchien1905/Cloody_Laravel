<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('storage_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_id')->unique(); // basic, 100gb, 200gb, 2tb, hoặc custom
            $table->string('name'); // Tên hiển thị
            $table->integer('storage_gb'); // Dung lượng GB
            $table->decimal('price_monthly', 12, 2)->default(0); // Giá tháng
            $table->decimal('price_yearly', 12, 2)->default(0); // Giá năm
            $table->text('features')->nullable(); // JSON array các tính năng
            $table->boolean('is_active')->default(true); // Có đang hoạt động
            $table->boolean('is_popular')->default(false); // Gói phổ biến
            $table->integer('sort_order')->default(0); // Thứ tự hiển thị
            $table->timestamps();
        });

        // Thêm dữ liệu mặc định
        DB::table('storage_plans')->insert([
            [
                'plan_id' => 'basic',
                'name' => 'Cơ bản',
                'storage_gb' => 1,
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => json_encode([
                    '1 GB dung lượng lưu trữ',
                    'Chia sẻ file cơ bản',
                    'Hỗ trợ qua email',
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plan_id' => '100gb',
                'name' => '100 GB',
                'storage_gb' => 100,
                'price_monthly' => 45000,
                'price_yearly' => round(45000 * 12 * 0.84),
                'features' => json_encode([
                    '100 GB dung lượng lưu trữ',
                    'Chia sẻ file không giới hạn',
                    'Hỗ trợ qua email và chat',
                    'Đồng bộ đa thiết bị',
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plan_id' => '200gb',
                'name' => '200 GB',
                'storage_gb' => 200,
                'price_monthly' => 69000,
                'price_yearly' => round(69000 * 12 * 0.84),
                'features' => json_encode([
                    '200 GB dung lượng lưu trữ',
                    'Chia sẻ file không giới hạn',
                    'Hỗ trợ ưu tiên 24/7',
                    'Đồng bộ đa thiết bị',
                    'Phục hồi file đã xóa',
                ]),
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plan_id' => '2tb',
                'name' => '2 TB',
                'storage_gb' => 2048,
                'price_monthly' => 225000,
                'price_yearly' => round(225000 * 12 * 0.84),
                'features' => json_encode([
                    '2 TB dung lượng lưu trữ',
                    'Chia sẻ file không giới hạn',
                    'Hỗ trợ ưu tiên 24/7',
                    'Đồng bộ đa thiết bị',
                    'Phục hồi file đã xóa',
                    'API access',
                ]),
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_plans');
    }
};
