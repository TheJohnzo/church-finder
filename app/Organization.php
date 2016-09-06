<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organization';

    public static function allIndexById()
    {
        $return = [];
        $orgs = self::all();
        foreach ($orgs as $org) {
            $return[$org->id] = $org->name;
        }
        return $return;
    }
}