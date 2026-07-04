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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total', 12, 2)->nullable();
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->default('midtrans');
            $table->string('snap_token')->nullable();
            $table->string('order_code')->unique();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_option')->nullable();
            $table->string('shipping_reference')->nullable();
            $table->string('courier_name')->nullable();
            $table->date('estimated_arrival')->nullable();
            $table->string('status')->default('production');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
