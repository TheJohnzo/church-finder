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


}
