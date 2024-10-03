<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultValueToTotalInTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('in_transactions', function (Blueprint $table) {
            // Menambahkan default value untuk kolom total
            $table->decimal('total', 10, 2)->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('in_transactions', function (Blueprint $table) {
            // Kembalikan perubahan jika migration dirollback
            $table->decimal('total', 10, 2)->nullable()->change();
        });
    }
}
