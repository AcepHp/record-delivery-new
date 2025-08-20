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
        Schema::create('delivery', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi',25);
            $table->datetime('tgl_bln_thn');
            $table->string('part_number',50);
            $table->string('lot_number', 30);
            $table->string('pic', 50);
            $table->integer('qty');
            $table->tinyInteger('flag');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery');
    }
};
