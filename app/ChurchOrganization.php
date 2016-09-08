<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChurchOrganization extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_organization';

    /**
     * Get the organizations for the church.
     */
    public function organization()
    {
        return $this->hasMany('App\Organization');
    }

    public static function addToOrganization($church_id, $organization_id)
    {
        $found = self::where('church_id', $church_id)
            ->where('organization_id', $organization_id)
            ->get();
        if (count($found) < 1) {
            $churchOrganization = new ChurchOrganization;
            $churchOrganization->church_id = $church_id;
            $churchOrganization->organization_id = $organization_id;
            $churchOrganization->save();
        }
    }

    public static function removeFromOrganization($church_id, $organization_id)
    {
        self::where('church_id', $church_id)
            ->where('organization_id', $organization_id)
            ->delete();
    }

}
