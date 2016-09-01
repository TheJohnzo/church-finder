<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrganizationCountry extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organization_country';

    public static function allByOrgIdCountryIdOnly($id)
    {
        $return = [];
        $countries = \App\OrganizationCountry::where('organization_id', $id)->select('country_id')->get();
        foreach ($countries as $c) {
            
            $return[] = $c['country_id'];
        }
        return $return;
    }

    public static function addToCountry($org_id, $country_id)
    {
        $found = self::where('organization_id', $org_id)
            ->where('country_id', $country_id)
            ->get();
        if (count($found) < 1) {
            $oc = new self;
            $oc->organization_id = $org_id;
            $oc->country_id = $country_id;
            $oc->save();
        }
    }

    public static function removeFromCountry($org_id, $country_id)
    {
        self::where('organization_id', $org_id)
            ->where('country_id', $country_id)
            ->delete();
    }

}
