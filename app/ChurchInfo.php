<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChurchInfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_info';

    /**
     * Returns all church info data based on langauge as array, indexed by church_id.
     */
    public static function allByLanguageIndexed($lang = 'en')
    {
        $infos = [];
        foreach(self::where('language', $lang)->cursor() as $info) {
        
            $infos[$info['church_id']] = $info;
        
        }
        return $infos;
    }

    /**
     * Returns all church info data based on church_id as array, indexed by language.
     */
    public static function allByChurchIdIndexed($church_id) 
    {
        $infos = [];
        foreach(self::where('church_id', $church_id)->cursor() as $info) {
        
            $infos[$info['language']] = $info;
        
        }
        return $infos;
    }

}
