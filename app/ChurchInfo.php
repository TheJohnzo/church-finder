<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Church;

class ChurchInfo extends Model
{
    /**
     * The attributes that are mass assignable.
     * Needs this for bulk data import
     *
     * @var array
     */
    protected $fillable = ['name'];
    
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

    public static function findOrCreateByName($name, $language="ja")
    {
        $churchInfo = self::where("name", $name)->where('language', $language)->first();
        if ($churchInfo !== null) {
            return $churchInfo;
        }

        // create church record to get parent ID.  
        $church = new Church;
        $church->save();

        // create info record and return it
        $churchInfo = new self;
        $churchInfo->name = $name;
        $churchInfo->language = $language;
        $churchInfo->church_id = $church->id;
        $churchInfo->save();

        return $churchInfo;
    }

}
