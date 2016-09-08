<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChurchMeetingTime extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_meeting_time';


    /**
     * Get the meeting time languages for the meeting.
     */
    public function language()
    {
        return $this->hasMany('App\ChurchMeetingTimeLanguage');
    }

}
