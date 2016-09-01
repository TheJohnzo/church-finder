<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'language';

    public static function allIndexByCode()
    {
        $return = [];
        $langs = self::all();
        foreach ($langs as $lang) {
            $return[$lang->code] = $lang;
        }
        return $return;
    }
}
