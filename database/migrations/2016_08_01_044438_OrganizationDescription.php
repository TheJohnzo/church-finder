<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OrganizationDescription extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organization_description', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description');
            $table->string('language');
            $table->integer('organization_id')->unsigned();
            $table->timestamps();

            $table->foreign('organization_id')->references('id')->on('organization');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('organization_description');
    }
}
