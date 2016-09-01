<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TagTranslation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tag_translation';

    public static function allByTagIdIndexByLanguage($tag_id)
    {
        $return = [];
        foreach(self::where('tag_id', $tag_id)->cursor() as $t)
        {
            $return[$t['language']] = $t['tag'];
        }
        return $return;
    }

}
