<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChurchMeetingTimeLanguage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'church_meeting_time_language';

    /**
     * Returns all meeting time data based on language as array, indexed by church_meeting_time_id.
     */
    public static function allIndexByMeetingTimeId()
    {
        $return = [];
        $times = self::all();
        foreach($times as $time) {

            $return[$time['church_meeting_time_id']][] = $time;

        }
        return $return;
    }

    /**
     * Returns all meeting time data based on langauge as array, indexed by language.
     */
    public static function allByMeetingIdIndexByLanguage($meeting_id)
    {
        $return = [];
        $times = self::where('church_meeting_time_id', $meeting_id)->get();
        foreach($times as $time) {

            $return[$time['language']] = 'checked';

        }
        return $return;
    }
}
