<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'country';

    public static function allIndexById()
    {
        $return = [];
        $countries = self::orderBy('prioritize', 'desc')->get();
        foreach ($countries as $c) {
            $return[$c->id] = $c->name;
        }
        return $return;
    }
}
