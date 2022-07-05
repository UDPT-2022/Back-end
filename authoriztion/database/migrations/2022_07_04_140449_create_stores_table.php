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
        Schema::dropIfExists('stores');
        Schema::create('stores', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->unique()->primary();
            $table->string('TEN_CUA_HANG')->nullable();
            $table->string('SDT')->nullable();
            $table->string('EMAIL')->nullable();
            $table->longText('DIA_CHI')->nullable();
            $table->longText('LOGO')->nullable();
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
        Schema::dropIfExists('stores');
    }
};
