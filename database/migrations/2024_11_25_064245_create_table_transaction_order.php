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
        Schema::create('transaction_order', function (Blueprint $table) {
            $table->id('transaction_order_id');
            $table->integer('users_id');
            $table->integer('customer_id');
            $table->integer('package_laundry_id');
            $table->datetime('order_date');
            $table->datetime('pick_up_date');
            $table->enum('status', ['new', 'on process', 'done']);
            $table->enum('payment_status', ['paid', 'unpaid']);
            $table->integer('weight');
            $table->integer('subtotal');
            $table->integer('total_price');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_transaction_order');
    }
};
