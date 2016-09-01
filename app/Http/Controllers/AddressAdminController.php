<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use App\Http\Requests;

class AddressAdminController extends Controller
{
    public function editChurchAddress($id)
    {
        $church = \App\Church::findorfail($id);
        $addresses = \App\ChurchAddress::where('church_id', $id)->get();

        //fake new addr allows the view to display a "create new" form after all the edit forms
        $fake_new_addr = new \App\ChurchAddress;
        $fake_new_addr->id = 'new';
        $addresses[] = $fake_new_addr;

        $data = [
            'church' => $church,
            'languages' => \App\Language::all(),
            'addresses' => $addresses,
            'address_labels' => \App\ChurchAddressLabel::allByChurchId($id),
            'msg' => session('message'),
            'address_page' => 'active',
        ];
        return view('admin/church_edit_address', $data);
    }

    public function insertChurchAddress($id, $new, Request $request)
    {
        $addr = new \App\ChurchAddress;
        $addr->church_id = $id;
        $addr->save();
        if ($request->primary) {
            $addr->makePrimary();
        } else {
            $addr->ifOnlyMakePrimary();
        }
        $address_id = $addr->id;

        $rules = [];
        $languages = \App\Language::all();
        foreach ($languages as $l) {
            $rules['addr_' . $l->code] = 'required';
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/' . $id . '/address')
                ->withInput()
                ->withErrors($validator);
        } else {

            foreach ($languages as $l) {
                $label = \App\ChurchAddressLabel::where('church_address_id', $address_id)
                    ->where('language', $l->code)
                    ->first();
                if (!$label) {
                    $label = new \App\ChurchAddressLabel;
                    $label->church_address_id = $address_id;
                    $label->language = $l->code;
                }
                $addrField = 'addr_' . $l->code;
                $label->addr = $request->$addrField;
                $label->save();
            }

            // redirect
            $request->session()->flash('message', 'Address saved!');
            return \Redirect::to('admin/church/edit/' . $id);
        }
    }

    public function updateChurchAddress($id, $address_id, Request $request)
    {
        $addr = \App\ChurchAddress::findorfail($address_id);
        if ($request->primary) {
            $addr->makePrimary();
        } else {
            $addr->ifOnlyMakePrimary();
        }

        $rules = [];
        $languages = \App\Language::all();
        foreach ($languages as $l) {
            $rules['addr_' . $l->code] = 'required';
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/' . $id . '/address')
                ->withInput()
                ->withErrors($validator);
        } else {

            foreach ($languages as $l) {
                $label = \App\ChurchAddressLabel::where('church_address_id', $address_id)
                    ->where('language', $l->code)
                    ->first();
                if (!$label) {
                    $label = new \App\ChurchAddressLabel;
                    $label->church_address_id = $address_id;
                    $label->language = $l->code;
                }
                $addrField = 'addr_' . $l->code;
                $label->addr = $request->$addrField;
                $label->save();
            }

            // redirect
            $request->session()->flash('message', 'Address saved!');
            return \Redirect::to('admin/church/edit/' . $id);
        }
    }

    public function lookupAddresses(Request $request)
    {
        //FIXME need to validate if no results found.
        $languages = \App\Language::all();
        $return = [];
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={latlon}&key=" . config('googlmapper.key') . "&language={lang}";
        $location = Mapper::location($request->input('addr'));
        foreach ($languages as $lang) {
            $url_new = str_replace(['{latlon}', '{lang}'], 
                [$location->getLatitude() . ',' . $location->getLongitude(), $lang->code],
                $url);
            $data = json_decode(file_get_contents($url_new), true);
            $return[$lang->code] = $data['results'][0]['formatted_address'];
        }
        return json_encode($return);
    }

    public function deleteChurchAddress($id, $address_id, Request $request)
    {
        $address = \App\ChurchAddress::findorfail($address_id);

        //Delete all the language labels for this address.
        \App\ChurchAddressLabel::where('church_address_id', $address_id)
            ->delete();

        //Delete all meeting languages relating to meetings with this address
        \App\ChurchMeetingTimeLanguage::whereIn(
            'church_meeting_time_id', 
            \App\ChurchMeetingTime::where('church_address_id', $address_id)->select('id')->get())
                ->delete();

        //Delete all meetings at this address
        \App\ChurchMeetingTime::where('church_address_id', $address_id)
            ->delete();

        //Finally, delete the address
        $address->delete();

        // redirect
        $request->session()->flash('message', 'Address <em>' . $address_id . '</em> deleted!');
        return \Redirect::to('admin/church/edit/' . $id);
    }

}
