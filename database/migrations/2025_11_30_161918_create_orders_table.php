<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Thông tin khách hàng
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone', 20);
            $table->text('user_address');
            $table->text('user_note')->nullable();
            
            // Thông tin đơn hàng
            $table->boolean('is_ship_user_same_user')->default(true);
            $table->string('status_order')->default('pending'); // pending, completed, failed, cancelled
            $table->string('status_payment')->default('unpaid'); // paid, unpaid
            $table->decimal('total_price', 15, 2);
            
            $table->timestamps();
            
            // Index để tăng tốc query
            $table->index('user_id');
            $table->index('status_order');
            $table->index('status_payment');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};