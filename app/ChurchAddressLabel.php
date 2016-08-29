<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ChurchAddressLabel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_address_label';

    /**
     * For a given $church_id, return all language variations of the address label, grouped by address_id
     */
    public static function allByChurchId($church_id)
    {
        $data = [];
        $addrs = DB::select('SELECT ca.id, cal.addr, l.code as language
        FROM church_address ca
        LEFT JOIN church_address_label cal ON ca.id = cal.church_address_id
        LEFT JOIN language l ON cal.language = l.code
        WHERE church_id = ? ORDER BY cal.language, cal.id', [$church_id]);
        foreach ($addrs as $a) {
            $data[$a->id][$a->language] = $a->addr;
        }
        return $data;
    }
}
