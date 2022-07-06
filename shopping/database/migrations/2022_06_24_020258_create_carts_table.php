<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('cart_details');
        Schema::dropIfExists('carts');
        Schema::create('carts', function (Blueprint $table) {
            $table->id('MA_GIO_HANG');
            $table->bigInteger('MA_NGUOI_DUNG');
            $table->int('TONG_TIEN')->default('0');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carts');
    }
};
