<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChurchAddressView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('CREATE VIEW church_address_view AS
            SELECT c.id as church_id, c.size_in_people, c.url, c.contact_phone, c.contact_email,
                a.longitude, a.latitude 
            FROM church c
            JOIN church_address a ON c.id = a.church_id
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW church_address_view');
    }
}
