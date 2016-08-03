<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChurchLanguage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('church_language', function (Blueprint $table) {
            $table->increments('id');
            $table->string('language');
            $table->integer('church_id')->unsigned();
            $table->timestamps();

            $table->foreign('church_id')->references('id')->on('church');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('church_language');
    }
}
