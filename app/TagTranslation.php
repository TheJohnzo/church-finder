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

    public static function allWithChurch($church_id, $request)
    {
        $languages = $request->languages;
        $search = $request->search;
        $where = 'WHERE 1=1';
        if (is_array($languages)) {
            $where .= ' AND l.code IN (\'' . implode("','", $languages) . '\')';
        }
        if ($search) {
            $where .= ' AND tt.tag_id IN (SELECT tag_id FROM tag_translation WHERE lower(tag) LIKE lower(?))';
        }
        $sql = '
        SELECT tt.*, l.primary_country, ct.id as tagged
        FROM 
        tag_translation tt
        JOIN language l ON tt.language = l.code
        LEFT JOIN ( SELECT * FROM church_tag WHERE church_id = ?) ct ON tt.tag_id = ct.tag_id
        ' . $where;

        return DB::select($sql, [$church_id, '%' . $search . '%']);
    }

}
