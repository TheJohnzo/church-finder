<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use App\Http\Requests;

class AddressAdminController extends AdminController
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

    public function editChurchAddressSingle($id, $address_id)
    {
        $church = \App\Church::findorfail($id);

        $addresses = [];
        if ($address_id > 0) {
            $addresses[] = \App\ChurchAddress::where('id', $address_id)->get();
        } else {
            //fake new addr allows the view to display a "create new" form after all the edit forms
            $fake_new_addr = new \App\ChurchAddress;
            $fake_new_addr->id = 'new';
            $addresses[] = $fake_new_addr;
        }

        $data = [
            'church' => $church,
            'languages' => \App\Language::all(),
            'addresses' => $addresses,
            'address_labels' => \App\ChurchAddressLabel::allByChurchId($id),
            'msg' => session('message'),
            'address_page' => 'active',
            'address_id' => $address_id,
        ];
        return view('admin/church_edit_address', $data);
    }

    public function insertChurchAddress($id, $new, Request $request)
    {
        $addr = new \App\ChurchAddress;
        return $this->postSave($id, $addr, $request);
    }

    public function updateChurchAddress($id, $address_id, Request $request)
    {
        $addr = \App\ChurchAddress::findorfail($address_id);

        return $this->postSave($id, $addr, $request);
    }

    /**
     * Get google friendly address from latlon
     */
    public function lookupAddresses(Request $request)
    {
        $return = \App\ChurchAddress::lookupAddresses($request->input('addr'));
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

    /**
     * Post save logic used by both isnert and update
     */
    public function postSave($id, $addr, $request)
    {
        $messages = [
            'required' => 'Please enter a valid address and click "Lookup Addresses"',
        ];
        $rules = [];
        $languages = \App\Language::all();
        foreach ($languages as $l) {
            $rules['addr_' . $l->code] = 'required';
        }

        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/' . $id . '/address/' . (($addr->id) ? $addr->id : 0))
                ->withInput()
                ->withErrors($validator);
        } else {

            if ($addr->id < 1) {
                $addr->church_id = $id;
                $addr->save();
            }

            foreach ($languages as $l) {
                $label = \App\ChurchAddressLabel::where('church_address_id', $addr->id)
                    ->where('language', $l->code)
                    ->first();
                if (!$label) {
                    $label = new \App\ChurchAddressLabel;
                    $label->church_address_id = $addr->id;
                    $label->language = $l->code;
                }
                $addrField = 'addr_' . $l->code;
                $label->addr = $request->$addrField;
                $label->save();
            }

            $addr->updateLatLongFromAddress();
            if ($request->primary) {
                $addr->makePrimary();
            } else {
                $addr->ifOnlyMakePrimary();
            }

            // redirect
            $request->session()->flash('message', 'Address saved!');
            return \Redirect::to('admin/church/edit/' . $id);
        }
    }

}
