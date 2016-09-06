<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    public static function allWithChurch($church_id, $languages)
    {
        $where = '';
        if (is_array($languages)) {
            $where = 'WHERE l.code IN (\'' . implode("','", $languages) . '\')';
        }
        return DB::select('
        SELECT tt.*, l.primary_country, ct.id as tagged
        FROM 
        tag_translation tt
        JOIN language l ON tt.language = l.code
        LEFT JOIN church_tag ct ON tt.tag_id = ct.tag_id
        ' . $where);
    }

}
