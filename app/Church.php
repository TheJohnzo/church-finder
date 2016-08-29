<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;

class Church extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church';

    /**
     * @param $latitude  Lat coordinate of search value
     * @param $longitude  Lon coordinate of search value
     * @param $distance  Max distance allowed in Meters, default 25
     */
    public static function findChurchesNearLatLon($latitude, $longitude, $distance = 20)
    {
        //TODO how will this handle multiple addresses???
        $q = "
        SELECT * FROM (
            SELECT c.*,(((acos(sin(( :lat *pi()/180)) * sin((`latitude`*pi()/180))+cos(( :lat2 *pi()/180)) * 
                    cos((`latitude`*pi()/180)) * cos((( :lon - `longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance
            FROM church_address_view c 
        ) foo
        WHERE distance <= :distance
        ORDER BY distance";
        $locations = DB::select($q, ['lat' => $latitude, 'lat2' => $latitude, 'lon' => $longitude, 'distance' => $distance]);
        return $locations;
    }

    public static function updateLatLongFromAddress($id)
    {
        foreach (\App\ChurchAddress::where('church_id', $id)->cursor() as $a) {

            $addressLabel = \App\ChurchAddressLabel::where('church_address_id', $a->id)
                ->where('language', 'en')//FIXME default only english during dev
                ->first();

            $location = Mapper::location($addressLabel['addr']);
            $a->latitude = $location->getLatitude();
            $a->longitude = $location->getLongitude();
            $a->save();
        }
    }

    public static function updateLatLongFromAddressAll()
    {
        $churches = self::all();
        foreach ($churches as $church) {
            self::updateLatLongFromAddress($church->id);
        }
    }

    //temp, create stub records for testing
    public static function insertFromNameAndAddress($name, $address) {
        $church = self::create();

        $churchAddress = new \App\ChurchAddress;
        $churchAddress->church_id = $church->id;
        $churchAddress->save();

        $churchAddressLabel = new \App\ChurchAddressLabel;
        $churchAddressLabel->church_address_id = $churchAddress->id;
        $churchAddressLabel->addr = $address;
        $churchAddressLabel->language = 'en';
        $churchAddressLabel->save();

        $info = new \App\ChurchInfo;
        $info->church_id = $church->id;
        $info->name = $name;
        $info->language = 'en';
        $info->save();
    }
    
}
