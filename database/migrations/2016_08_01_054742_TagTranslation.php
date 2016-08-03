<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TagTranslation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag_translation', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag');
            $table->string('language');
            $table->integer('tag_id')->unsigned();
            $table->timestamps();

            $table->foreign('tag_id')->references('id')->on('tag');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('tag_translation');
    }
}
