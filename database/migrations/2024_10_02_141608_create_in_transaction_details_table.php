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
        Schema::create('in_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('in_transaction_id')->constrained('in_transactions')->cascadeOnDelete('');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('qty');
            $table->string('unit');
            $table->integer('qty_in_pcs')->default(0);
            $table->decimal('price', 10, 2);
            $table->decimal('amount', 10, 2)->default(0); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_transaction_details');
    }
};
