<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Church extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church';

    /**
     * Get the info for the church.
     */
    public function info()
    {
        return $this->hasMany('App\ChurchInfo');
    }

    /**
     * Get the address for the church.
     */
    public function address()
    {
        return $this->hasMany('App\ChurchAddress');
    }

    /**
     * Get the meetings for the church.
     */
    public function meetingtime()
    {
        return $this->hasMany('App\ChurchMeetingTime');
    }

    /**
     * Get the organizations for the church.
     */
    public function organization()
    {
        return $this->belongsToMany('App\Organization');
    }

    /**
     * Get the tags for the church.
     */
    public function tag()
    {
        return $this->belongsToMany('App\Tag');
    }

    /**
     * @param $latitude  Lat coordinate of search value
     * @param $longitude  Lon coordinate of search value
     * @param $distance  Max distance allowed in Meters, default 20
     */
    public static function allChurchesNearLatLonRaw($latitude, $longitude, $distance = 20)
    {
        $temp_table_name = str_replace(['-', '.', ' '], '_', "temp_" . $latitude . '_' . $longitude . '_' . $distance . '_' . microtime());
        $temp_q = "
            CREATE TEMPORARY TABLE $temp_table_name 
            SELECT c.id church_id, c.*,(((acos(sin(( $latitude *pi()/180)) * sin((latitude*pi()/180))+cos(( $latitude *pi()/180)) * 
                    cos((latitude*pi()/180)) * cos((( $longitude - longitude)*pi()/180))))*180/pi())*60*1.1515*1.609344) as distance
            FROM church c 
            JOIN church_address a ON (c.id = a.church_id)
        ";
        DB::select($temp_q);

        //TODO how will this handle multiple addresses???
        $q = "
        SELECT church_id, distance FROM $temp_table_name
        WHERE distance <= :distance
        ORDER BY distance";
        $data = DB::select($q, ['distance' => $distance]);

        $drop_q = "DROP TEMPORARY TABLE $temp_table_name";
        DB::select($drop_q);

        return $data;
    }

    /**
     * @param $latitude  Lat coordinate of search value
     * @param $longitude  Lon coordinate of search value
     * @param $distance  Max distance allowed in Meters, default 20
     */
    public static function allChurchesNearLatLon($latitude, $longitude, $distance = 20)
    {
        $locations = self::allChurchesNearLatLonRaw($latitude, $longitude, $distance);

        if (count($locations) == 0) {
            return [];
        }

        foreach ($locations as $l) {
            $ids[] = $l->church_id;
            $distances[$l->church_id] = $l->distance;
        }
        $churches = self::whereIn('id', $ids)->get();
        foreach ($churches as &$c) {
            $c->distance = $distances[$c->id];//not great to add custom param, but needed for search & display.
        }
        return $churches->sortBy('distance');
    }

    /**
     * @param $latitude  Lat coordinate of search value
     * @param $longitude  Lon coordinate of search value
     * @param $distance  Max distance allowed in Meters, default 20
     */
    public static function allChurchesNearLatLonTags($latitude, $longitude, $distance = 20, $tags)
    {
        $locations = self::allChurchesNearLatLonRaw($latitude, $longitude, $distance);
        foreach ($locations as $l) {
            $ids[] = $l->church_id;
            $distances[$l->church_id] = $l->distance;
        }
        $churches = self::whereIn('id', $ids)
            ->whereRaw('id IN (SELECT church_id FROM church_tag WHERE tag_id IN (' . implode(',', $tags) . '))')
            ->get();
        foreach ($churches as &$c) {
            $c->distance = $distances[$c->id];//not great to add custom param, but needed for search & display.
        }
        return $churches->sortBy('distance');
    }

    /**
     * @param $tags array of tag_ids to filter by
     */
    public static function allChurchesByTags($tags)
    {
        $locations = self::whereRaw('id IN (SELECT church_id FROM church_tag WHERE tag_id IN (' . implode(',', $tags) . '))')->get();
        //TODO Sort by churches with the most matched tags out of availble tags
        return $locations;
    }


    public static function updateLatLongFromAddressByChurchId($id)
    {
        foreach (\App\ChurchAddress::where('church_id', $id)->cursor() as $a) {
            $a->updateLatLongFromAddress();
        }
    }

    public static function updateLatLongFromAddressAll()
    {
        $churches = self::all();
        foreach ($churches as $church) {
            self::updateLatLongFromAddressByChurchId($church->id);
        }
    }

    public static function allMissingInfo()
    {
        $q = "
        SELECT c.id as church_id, count(ci.id) 
        FROM church c
        LEFT JOIN church_info ci ON c.id = ci.church_id
        WHERE ci.name <> ''
        GROUP BY c.id
        HAVING count(ci.id) < (SELECT count(id) FROM language)";
        $return = [];
        foreach (DB::select($q) as $r) {
            $return[$r->church_id] = true;
        }
        return $return;
    }

    public static function allMissingAddress()
    {
        $q = "
        SELECT c.id as church_id
        FROM church c
        LEFT JOIN church_address ca ON c.id = ca.church_id
        GROUP BY c.id
        HAVING count(ca.id) < 1

        UNION

        SELECT c.id as church_id
        FROM church c
        JOIN church_address ca ON c.id = ca.church_id
        LEFT JOIN church_address_label cal ON ca.id = cal.church_address_id
        GROUP BY c.id
        HAVING count(cal.id) < (SELECT count(id) FROM language)
        ";
        $return = [];
        foreach (DB::select($q) as $r) {
            $return[$r->church_id] = true;
        }
        return $return;
    }

    public static function allMissingMeetingTime()
    {
        $q = "
        SELECT c.id as church_id, count(cmt.id) 
        FROM church c
        LEFT JOIN church_meeting_time cmt ON c.id = cmt.church_id
        GROUP BY c.id
        HAVING count(cmt.id) < 1";
        $return = [];
        foreach (DB::select($q) as $r) {
            $return[$r->church_id] = true;
        }
        return $return;
    }

    public static function allMissingContact()
    {
        $q = "
        SELECT c.id as church_id 
        FROM church c
        WHERE length(contact_email) = 0 OR length(contact_phone) = 0";
        $return = [];
        foreach (DB::select($q) as $r) {
            $return[$r->church_id] = true;
        }
        return $return;
    }

}
