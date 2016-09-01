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

    /**
     * For a given $church_id, return all language variations of the address
     */
    public static function byChurchIdAndLanguageIndexByAddressId($church_id, $language)
    {
        $return = [];
        $address = DB::select('SELECT ca.*, cal.addr, language
        FROM church_address ca
        JOIN church_address_label cal ON ca.id = cal.church_address_id
        WHERE church_id = ? AND cal.language = ? ORDER BY cal.language, cal.id', [$church_id, $language]);
        foreach ($address as $a) {
            $return[$a->id] = $a->addr;
        }
        return $return;
    }

    /**
     * For current address, make primary, and make all other addresses not primary for the same church.
     * TODO model onAfterSave callback somewhere?
     */
    public function makePrimary()
    {
        $this->primary = 1;
        $this->save();
        self::where('church_id', $this->church_id)
            ->where('id', '!=', $this->id)
            ->update(['primary' => 0]);
    }

    /**
     * If there's only one address for a church, force it to be primary.
     */
    public function ifOnlyMakePrimary()
    {
        if (self::where('church_id', $this->church_id)->count() === 1) {
            $this->primary = 1;
            $this->save();
        };
    }

    public static function ifOnlyMakePrimaryAll()
    {
        $addr = self::all();
        foreach ($addr as $a) {
            $a->ifOnlyMakePrimary();
        }
    }

}
