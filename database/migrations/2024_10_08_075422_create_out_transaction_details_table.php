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
        Schema::create('out_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('out_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('qty');
            $table->string('unit');
            $table->integer('qty_in_pcs')->default(0);
            $table->decimal('price');
            $table->decimal('amount')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('out_transaction_details');
    }
};
