<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChurchMeetingTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('church_meeting_time', function (Blueprint $table) {
            $table->increments('id');
            $table->string('time');
            $table->integer('day_of_week');//Sunday = 0, Saturday = 6
            $table->string('language');
            $table->integer('church_id')->unsigned();
            $table->integer('church_address_id')->unsigned();
            $table->timestamps();

            $table->foreign('church_id')->references('id')->on('church');
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
        Schema::drop('church_meeting_time');
    }
}
