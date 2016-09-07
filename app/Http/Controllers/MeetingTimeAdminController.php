<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use App\Http\Requests;

class MeetingTimeAdminController extends Controller
{
    public function index($id)
    {
        $church = \App\Church::findorfail($id);
        $meeting_times = \App\ChurchMeetingTime::where('church_id', $id)->paginate(20);
        $meeting_time_languages = \App\ChurchMeetingTimeLanguage::allIndexByMeetingTimeId();
        $data = [
            'church' => $church,
            'meeting_times' => $meeting_times,
            'meeting_time_languages' => $meeting_time_languages,
            'languages' => \App\Language::allIndexByCode(),
            'addresses' => \App\ChurchAddress::byChurchIdAndLanguageIndexByAddressId($id, 'en'),//FIXME only english in admin
            'days' => $this->getDays(),
            'msg' => session('message'),
            'sizes' => \App\ChurchSize::all(),
            'meeting_page' => 'active',
        ];
        return view('admin/meeting_times', $data);
    }

    public function newMeetingTime($id)
    {
        $church = \App\Church::findorfail($id);
        $data = [
            'church' => $church,
            'languages' => \App\Language::all(),
            'addresses' => \App\ChurchAddress::byChurchIdAndLanguageIndexByAddressId($id, 'en'),//FIXME only english in admin
            'days' => $this->getDays(),
            'msg' => session('message'),
            'meeting_page' => 'active',
        ];
        return view('admin.meeting_time_new', $data);
    }

    public function editMeetingTime($id, $meeting_id)
    {
        $church = \App\Church::findorfail($id);
        $time = \App\ChurchMeetingTime::findorfail($meeting_id);
        $meeting_time_languages = \App\ChurchMeetingTimeLanguage::allByMeetingIdIndexByLanguage($meeting_id);
        $data = [
            'church' => $church,
            'time' => $time,
            'selected_languages' => $meeting_time_languages,
            'languages' => \App\Language::all(),
            'addresses' => \App\ChurchAddress::byChurchIdAndLanguageIndexByAddressId($id, 'en'),//FIXME only english in admin
            'days' => $this->getDays(),
            'msg' => session('message'),
            'meeting_page' => 'active',
        ];
        return view('admin.meeting_time_edit', $data);
    }

    public function insertMeetingTime($id, Request $request)
    {
        $rules = array(
            'day_of_week' => 'required',
            'time' => 'required|date_format:H:i',
            'languages' => 'required',
            'address' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/' . $id . '/meetingtime/new')
                ->withInput()
                ->withErrors($validator);
        } else {
            //save core record fields
            $meetingtime = new \App\ChurchMeetingTime;
            $meetingtime->church_id = $id;
            $meetingtime->time = $request->time;
            $meetingtime->day_of_week = $request->day_of_week;
            $meetingtime->church_address_id = $request->address;
            $meetingtime->save();

            $languages = \App\Language::all();
            //save church info & languages spoken
            foreach ($languages as $l) {

                //udpate languages spoken at service
                $cmlang = \App\ChurchMeetingTimeLanguage::where('church_meeting_time_id', $meetingtime->id)
                    ->where('language', $l->code)
                    ->first();
                if (is_array($request->languages) && in_array($l->code, $request->languages) && !$cmlang) {
                    $cmlang = new \App\ChurchMeetingTimeLanguage;
                    $cmlang->language = $l->code;
                    $cmlang->church_meeting_time_id = $meetingtime->id;
                    $cmlang->save();
                } else if (is_array($request->languages) && !in_array($l->code, $request->languages) && $cmlang) {
                    $cmlang->delete();
                }
            }
            if (!is_array($request->languages)) {
                \App\ChurchMeetingTimeLanguage::where('church_meeting_time_id', $meetingtime->id)
                    ->delete();
            }

            // redirect
            $request->session()->flash('message', 'Meeting time <em>' . $meetingtime->id . '</em> saved!');
            return \Redirect::to('admin/church/' . $id . '/meetingtime');
        }
    }

    public function updateMeetingTime($id, $meeting_id, Request $request)
    {
        $rules = array(
            'day_of_week' => 'required',
            'time' => 'required|date_format:H:i',
            'languages' => 'required',
            'address' => 'required',
        );

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/' . $id . '/meetingtime/edit/' . $meeting_id)
                ->withInput()
                ->withErrors($validator);
        } else {
            //save core record fields
            $meetingtime = \App\ChurchMeetingTime::findorfail($meeting_id);
            $meetingtime->time = $request->time;
            $meetingtime->day_of_week = $request->day_of_week;
            $meetingtime->church_address_id = $request->address;
            $meetingtime->save();

            $languages = \App\Language::all();
            //save church info & languages spoken
            foreach ($languages as $l) {

                //udpate languages spoken at service
                $cmlang = \App\ChurchMeetingTimeLanguage::where('church_meeting_time_id', $meetingtime->id)
                    ->where('language', $l->code)
                    ->first();
                if (is_array($request->languages) && in_array($l->code, $request->languages) && !$cmlang) {
                    $cmlang = new \App\ChurchMeetingTimeLanguage;
                    $cmlang->language = $l->code;
                    $cmlang->church_meeting_time_id = $meetingtime->id;
                    $cmlang->save();
                } else if (is_array($request->languages) && !in_array($l->code, $request->languages) && $cmlang) {
                    $cmlang->delete();
                }
            }
            if (!is_array($request->languages)) {
                \App\ChurchMeetingTimeLanguage::where('church_meeting_time_id', $meetingtime->id)
                    ->delete();
            }

            // redirect
            $request->session()->flash('message', 'Meeting time <em>' . $meetingtime->id . '</em> saved!');
            return \Redirect::to('admin/church/' . $id . '/meetingtime');
        }
    }

    public function deleteMeetingTime($id, $meeting_id, Request $request)
    {
        $time = \App\ChurchMeetingTime::findorfail($meeting_id);
        \App\ChurchMeetingTimeLanguage::where('church_meeting_time_id', $meeting_id)
            ->delete();
        $time->delete();

        // redirect
        $request->session()->flash('message', 'Meeting time <em>' . $meeting_id . '</em> deleted!');
        return \Redirect::to('admin/church/' . $id . '/meetingtime');
    }

    protected function getDays()
    {
        return [
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }

}
