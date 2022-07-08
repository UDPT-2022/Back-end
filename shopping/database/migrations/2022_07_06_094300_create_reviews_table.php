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
        Schema::dropIfExists('reviews');
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('MA_REVIEW');
            $table->bigInteger('MA_SP')->unsigned();
            $table->bigInteger('MA_NGUOI_DUNG')->unsigned();
            $table->string('TEN');
            $table->longText('DANH_GIA')->nullable();

            $table->foreign('MA_SP')->references('MA_SP')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('reviews');
    }
};
