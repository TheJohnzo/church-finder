<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationInfo extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organization_info';


    /**
     * Returns all church info data based on church_id as array, indexed by language.
     */
    public static function allByOrgIdIndexByLanguage($org_id) 
    {
        $infos = [];
        foreach(self::where('organization_id', $org_id)->cursor() as $info) {
        
            $infos[$info['language']] = $info;
        
        }
        return $infos;
    }

    public static function findOrCreateByName($name, $language="ja")
    {
        $orgInfo = self::where("name", $name)->where('language', $language)->first();
        if ($orgInfo !== null) {
            return $orgInfo;
        }

        // create church record to get parent ID.  
        $org = new Organization;
        $org->save();

        // create info record and return it
        $orgInfo = new self;
        $orgInfo->organization_id = $org->id;
        $orgInfo->name = $name;
        $orgInfo->language = $language;
        $orgInfo->save();

        return $orgInfo;
    }

}
