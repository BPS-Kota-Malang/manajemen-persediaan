<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutTransactionDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('out_transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('out_transaction_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->integer('qty');
            $table->integer('qty_in_pcs')->nullable();
            $table->string('unit');
            $table->timestamps();
        });
        
    }

    public function down()
    {
        Schema::dropIfExists('out_transaction_details');
    }
}
