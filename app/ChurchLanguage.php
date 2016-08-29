<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChurchLanguage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_language';

    public static function allIndexByLanguage($church_id)
    {
        $return = [];
        foreach (self::where('church_id', $church_id)->cursor() as $key => $lang) {
            $return[$lang->language] = 'checked';
        }
        return $return;
    }
}
