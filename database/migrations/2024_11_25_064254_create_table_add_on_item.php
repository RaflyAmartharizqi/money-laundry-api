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
        Schema::create('add_on_item', function (Blueprint $table) {
            $table->id('add_on_item_id');
            $table->integer('transaction_order_id');
            $table->string('item_name');
            $table->integer('quantity');
            $table->integer('price_per_item');
            $table->integer('subtotal');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_add_on_item');
    }
};
