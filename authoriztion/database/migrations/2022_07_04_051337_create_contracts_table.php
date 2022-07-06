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
        Schema::dropIfExists('contracts');
        Schema::create('contracts', function (Blueprint $table) {
            $table->id('MA_HOP_DONG');
            
            $table->enum('LOAI', ['SHIPPER', 'SELLER']);
            $table->date('NGAY_KY')->nullable();
            $table->date('NGAY_HIEU_LUC')->nullable();
            $table->date('NGAY_KET_THUC')->nullable();
            $table->longText('GIAY_CHUNG_NHAN_AN_TOAN')->nullable();
            $table->longText('GIAY_PHEP_KINH_DOANH')->nullable();
            
            $table->bigInteger('id')->unsigned();
            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
            
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
        Schema::dropIfExists('contracts');
    }
};
