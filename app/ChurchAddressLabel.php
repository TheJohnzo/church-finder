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
    
    public static function findOrCreateByAddr($addr, $language, $church_id)
    {
        $churchAddress = ChurchAddress::where('church_id', $church_id)->where('primary', 1)->first();
        if ($churchAddress !== null) {
            $addressLabel = self::where("addr", $addr)
                ->where('language', $language)
                ->where('church_address_id', $churchAddress->id)
                ->first();
            if ($addressLabel !== null) {
                return $addressLabel;
            } else {
                echo "not found";
            }
        }

        // create church record to get parent ID.  
        $churchAddress = new ChurchAddress;
        $churchAddress->church_id = $church_id;
        $churchAddress->primary = 1;
        $churchAddress->save();

        // create info record and return it
        $addressLabel = new ChurchAddressLabel;
        $addressLabel->addr = $addr;
        $addressLabel->language = $language;
        $addressLabel->church_address_id = $churchAddress->id; 
        $addressLabel->save();

        return $addressLabel;
    }
}
