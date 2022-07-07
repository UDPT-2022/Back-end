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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('MA_DON_HANG')->unsigned();
            $table->bigInteger('MA_SP')->unsigned();

            $table->foreign('MA_SP')->references('MA_SP')->on('products')->onDelete('cascade');
            $table->foreign('MA_DON_HANG')->references('MA_DON_HANG')->on('orders')->onDelete('cascade');

            $table->integer('SO_LUONG');
            $table->integer('DON_GIA');
            $table->integer('GIA');

            $table->unique(['MA_DON_HANG', 'MA_SP']);
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
        Schema::dropIfExists('order_details');
    }
};
