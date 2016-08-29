<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ChurchAddress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_address';
    
    /**
     * For a given $church_id, return all language variations of the address
     */
    public static function byChurchIdWithLanguages($church_id)
    {
        return DB::select('SELECT ca.*, cal.addr, language
        FROM church_address ca
        JOIN church_address_label cal ON ca.id = cal.church_address_id
        WHERE church_id = ? ORDER BY cal.language, cal.id', [$church_id]);
    }
}
