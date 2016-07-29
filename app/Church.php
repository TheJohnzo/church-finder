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
        $q = "
        SELECT * FROM (
            SELECT *,(((acos(sin(( :lat *pi()/180)) * sin((`latitude`*pi()/180))+cos(( :lat2 *pi()/180)) * 
                    cos((`latitude`*pi()/180)) * cos((( :lon - `longitude`)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance
            FROM church
        ) foo
        WHERE distance <= :distance
        ORDER BY distance";
        $locations = DB::select($q, ['lat' => $latitude, 'lat2' => $latitude, 'lon' => $longitude, 'distance' => $distance]);
        return $locations;
    }

    public static function updateLatLongFromAddress($id)
    {
        $church = self::find($id);
        $location = Mapper::location($church['addr']);
        $church->latitude = $location->getLatitude();
        $church->longitude = $location->getLongitude();
        $church->save();
    }

    public static function updateLatLongFromAddressAll()
    {
        $churches = self::all();
        foreach ($churches as $church) {
            self::updateLatLongFromAddress($church->id);
        }
    }

}
