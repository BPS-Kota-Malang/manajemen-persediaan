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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            //`$table->integer('price');
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->enum('unit_1', ['box', 'pack']); // Opsi untuk unit_1
            $table->enum('unit_2', ['pcs', 'rim']); // Opsi untuk unit_2
            $table->integer('conversion_rate')->default(12); // Default 1 berarti 1 pcs
            $table->integer('stok')->default(0);
            //$table->foreignId('unit_id')->constrained('units')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
