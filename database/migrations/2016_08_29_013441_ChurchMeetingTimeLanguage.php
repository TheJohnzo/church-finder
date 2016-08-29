<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChurchMeetingTimeLanguage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('church_meeting_time_language', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('church_meeting_time_id')->unsigned();
            $table->string('language');
            $table->timestamps();

            $table->foreign('church_meeting_time_id')->references('id')->on('church_meeting_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('church_meeting_time_language');
    }
}
