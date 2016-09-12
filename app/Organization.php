<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organization';

    /**
     * Get the info for the church.
     */
    public function info()
    {
        return $this->hasMany('App\OrganizationInfo');
    }

    /**
     * Get the countries for the organization.
     */
    public function countries()
    {
        return $this->belongsToMany('App\Country', 'organization_country');
    }

    public static function allIndexById()
    {
        $return = [];
        $orgs = self::all();
        foreach ($orgs as $org) {
            $return[$org->id] = $org->name;
        }
        return $return;
    }
}
