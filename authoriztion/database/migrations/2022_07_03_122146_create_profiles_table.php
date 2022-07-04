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
        Schema::dropIfExists('profiles');
        Schema::create('profiles', function (Blueprint $table) {
            $table->id('MA_NGUOI_DUNG');
            $table->string('TEN');
            $table->string('CMND');
            $table->string('SDT');
            $table->date('NGAY_SINH');
            $table->longText('DIA_CHI')->nullable();
            
            $table->bigInteger('id')->unsigned();
            $table->string('VAI_TRO');

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
        Schema::dropIfExists('profiles');
    }
};
