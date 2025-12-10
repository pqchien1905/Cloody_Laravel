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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('user_subscriptions')->onDelete('set null');
            $table->string('plan_id'); // basic, 100gb, 200gb, 2tb
            $table->string('plan_name');
            $table->integer('storage_gb');
            $table->string('billing_cycle'); // monthly, yearly
            $table->decimal('amount', 12, 2); // Số tiền thanh toán
            $table->string('currency', 3)->default('VND');
            $table->string('payment_method')->default('vnpay'); // vnpay, momo, zalopay, etc.
            $table->string('payment_status')->default('pending'); // pending, processing, completed, failed, cancelled
            $table->string('transaction_id')->nullable(); // Mã giao dịch từ payment gateway
            $table->string('vnpay_txn_ref')->nullable(); // Mã tham chiếu VNPay
            $table->text('vnpay_response')->nullable(); // Response từ VNPay (JSON)
            $table->timestamp('paid_at')->nullable(); // Thời gian thanh toán thành công
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'payment_status']);
            $table->index('vnpay_txn_ref');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
