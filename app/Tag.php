<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tag';

    /**
     * Get the organizations for the church.
     */
    public function translation()
    {
        return $this->hasMany('App\TagTranslation');
    }

}
