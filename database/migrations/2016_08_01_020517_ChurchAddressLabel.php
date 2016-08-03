<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChurchAddressLabel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('church_address_label', function (Blueprint $table) {
            $table->increments('id');
            $table->string('addr');
            $table->string('language');
            $table->integer('church_address_id')->unsigned();
            $table->timestamps();

            $table->foreign('church_address_id')->references('id')->on('church_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('church_address_label');
    }
}
