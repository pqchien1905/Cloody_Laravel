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
        Schema::create('folder_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('folder_id')->constrained()->onDelete('cascade');
            $table->foreignId('shared_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('shared_with')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('share_token')->unique();
            $table->enum('permission', ['view', 'download', 'edit'])->default('view');
            $table->boolean('is_public')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['folder_id', 'shared_with']);
            $table->index('share_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folder_shares');
    }
};
