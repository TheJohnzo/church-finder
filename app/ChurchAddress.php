<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;

class ChurchAddress extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_address';

    /**
     * Get the address for the church.
     */
    public function label()
    {
        return $this->hasMany('App\ChurchAddressLabel');
    }

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

    public function updateLatLongFromAddress()
    {
        $addressLabel = \App\ChurchAddressLabel::where('church_address_id', $this->id)
            ->where('language', 'ja')# FIXME no defaults
            ->first();

        $location = Mapper::location($addressLabel['addr']);
        $this->latitude = $location->getLatitude();
        $this->longitude = $location->getLongitude();
        $this->save();
        
        # TODO only save when called from import
        $formattedAddresses = $this->lookupAddresses($addressLabel['addr']);
        foreach ($formattedAddresses as $language => $fadd) {
            $updatedAddressLabel = \App\ChurchAddressLabel::where('church_address_id', $addressLabel->church_address_id)->where('language', $language)->first();
            if ($updatedAddressLabel === NULL) {
                $updatedAddressLabel = new \App\ChurchAddressLabel;
                $updatedAddressLabel->language = $language;
                $updatedAddressLabel->church_address_id = $addressLabel->church_address_id;
            }
            $updatedAddressLabel->addr = $fadd;
            $updatedAddressLabel->save();
        }
    }

    public static function lookupAddresses($addr)
    {
        $languages = \App\Language::all();
        $return = [];
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={latlon}&key=" . config('googlmapper.key') . "&language={lang}";
        try {
            $location = Mapper::location($addr);
        } catch (\Exception $e) {
            return 'NOT_FOUND: ' . $e->getMessage();
        }
        foreach ($languages as $lang) {
            $url_new = str_replace(['{latlon}', '{lang}'], 
                [$location->getLatitude() . ',' . $location->getLongitude(), $lang->code],
                $url);
            $data = json_decode(file_get_contents($url_new), true);
            $return[$lang->code] = $data['results'][0]['formatted_address'];
        }
        return $return;
    }
}
