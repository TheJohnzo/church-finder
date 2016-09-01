<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChurchSize extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_size';

    public static function allIndexBySize()
    {
        $return = [];
        $sizes = self::all();
        foreach ($sizes as $size) {
            $return[$size->text] = $size->text;
        }
        return $return;
    }
}
