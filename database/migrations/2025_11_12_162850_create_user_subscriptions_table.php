<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan_id'); // basic, 100gb, 200gb, 2tb
            $table->string('plan_name'); // Tên gói
            $table->integer('storage_gb'); // Dung lượng lưu trữ (GB)
            $table->string('billing_cycle'); // monthly, yearly
            $table->decimal('price', 12, 2); // Giá đã thanh toán
            $table->timestamp('starts_at')->nullable(); // Ngày bắt đầu
            $table->timestamp('expires_at')->nullable(); // Ngày hết hạn (null nếu là gói miễn phí hoặc vĩnh viễn)
            $table->boolean('is_active')->default(true); // Trạng thái kích hoạt
            $table->string('payment_status')->default('pending'); // pending, paid, failed, cancelled
            $table->text('notes')->nullable(); // Ghi chú
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
