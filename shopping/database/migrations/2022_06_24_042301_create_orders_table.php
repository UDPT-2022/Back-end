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
        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::create('orders', function (Blueprint $table) {
            $table->id('MA_DON_HANG');
            $table->bigInteger('MA_NGUOI_DUNG');
            $table->bigInteger('MA_CUA_HANG');
            $table->longText('MA_SHIPPER')->nullable();
            $table->longText('DIA_CHI');
            $table->string('SDT');
            $table->integer('TONG_TIEN')->default('0');
            $table->string('TRANG_THAI')->nullable();
            
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
        Schema::dropIfExists('orders');
    }
};
