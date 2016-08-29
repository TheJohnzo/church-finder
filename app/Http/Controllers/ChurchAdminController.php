<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use App\Http\Requests;

class ChurchAdminController extends Controller
{
    public function index()
    {
        $churches = \App\Church::paginate(20);
        $data = [
            'churches' => $churches,
            'churchInfo' => \App\ChurchInfo::allByLanguageIndexed('en'),//FIXME only english during dev
            'msg' => session('message'),
            'sizes' => \App\ChurchSize::all(),
        ];
        return view('admin/church', $data);
    }

    public function newChurch()
    {
        $data = [
            'sizes' => \App\ChurchSize::all(),
            'languages' => \App\Language::all(),
        ];
        return view('admin/new_church', $data);
    }

    public function insertChurch(Request $request)
    {
        $rules = array(
            'name'          => 'required',
            'contact_email' => 'email',
            'url'           => 'url',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/new/')
                ->withInput()
                ->withErrors($validator);
        } else {
            $church = \App\Church::create();
            $church->name = $request->name;
            $church->size_in_people = $request->size_in_people;
            $church->url = $request->url;
            $church->contact_phone = $request->contact_phone;
            $church->contact_email = $request->contact_email;
            $church->save();
            // redirect
            $request->session()->flash('message', 'Church record <em>' . $church->name . '</em> saved!');
            return \Redirect::to('/admin/church');
        }
        return view('admin/new_church');
    }

    public function editChurch($id)
    {
        $church = \App\Church::findorfail($id);
        $church_organizations_raw = \App\ChurchOrganization::where('church_id', '=', $id)
            ->select('organization_id')
            ->get();
        $church_organizations = [];
        foreach ($church_organizations_raw as $c_org) {
            $church_organizations[] = $c_org['organization_id'];
        }

        $data = [
            'church' => $church,
            'infos' => \App\ChurchInfo::allByChurchIdIndexed($id),
            'organizations' => \App\Organization::all(),
            'church_organizations' => $church_organizations,
            'sizes' => \App\ChurchSize::all(),
            'languages' => \App\Language::all(),
            'addresses' => \App\ChurchAddress::where('church_id', $id)->get(),
            'address_labels' => \App\ChurchAddressLabel::allByChurchId($id),
            'church_languages' => \App\ChurchLanguage::allIndexByLanguage($id),
            'msg' => session('message'),
            'church_page' => 'active',
        ];
        return view('admin/edit_church', $data);
    }

    public function editChurchAddress($id)
    {
        $church = \App\Church::findorfail($id);

        $data = [
            'church' => $church,
            'languages' => \App\Language::all(),
            'addresses' => \App\ChurchAddress::where('church_id', $id)->get(),
            'address_labels' => \App\ChurchAddressLabel::allByChurchId($id),
            'msg' => session('message'),
            'address_page' => 'active',
        ];
        return view('admin/edit_church_address', $data);
    }

    public function updateChurchAddress($id, $address_id, Request $request)
    {
        $rules = [];
        $languages = \App\Language::all();
        foreach ($languages as $l) {
            $rules['addr_' . $l->code] = 'required';
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/edit/' . $id . '/address')
                ->withInput()
                ->withErrors($validator);
        } else {

            foreach ($languages as $l) {
                $label = \App\ChurchAddressLabel::where('church_address_id', $address_id)
                    ->where('language', $l->code)
                    ->first();
                if (!$label) {
                    $label = new \App\ChurchAddressLabel;
                    $label->church_address_id = $id;
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

    public function updateChurch($id, Request $request)
    {
        $rules = array(
            'contact_email' => 'email',
            'url'           => 'url',
        );
        $languages = \App\Language::all();
        foreach ($languages as $l) {
            $rules['name_' . $l->code] = 'required';
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/edit/' . $id)
                ->withInput()
                ->withErrors($validator);
        } else {
            //save core record fields
            $church = \App\Church::find($id);
            $church->size_in_people = $request->size_in_people;
            $church->url = $request->url;
            $church->contact_phone = $request->contact_phone;
            $church->contact_email = $request->contact_email;
            $church->save();

            //save church info & languages spoken
            foreach ($languages as $l) {
                //update church info
                $info = \App\ChurchInfo::where('church_id', $id)
                    ->where('language', $l->code)
                    ->first();
                if (!$info) {
                    $info = new \App\ChurchInfo;
                    $info->church_id = $id;
                    $info->language = $l->code;
                }
                $nameField = 'name_' . $l->code;
                $descriptionField = 'description_' . $l->code;
                $info->name = $request->$nameField;
                $info->description = $request->$descriptionField;
                $info->save();

                //udpate languages spoken at church
                $clang = \App\ChurchLanguage::where('church_id', $id)
                    ->where('language', $l->code)
                    ->first();
                if (is_array($request->languages) && in_array($l->code, $request->languages) && !$clang) {
                    $clang = new \App\ChurchLanguage;
                    $clang->language = $l->code;
                    $clang->church_id = $id;
                    $clang->save();
                } else if (is_array($request->languages) && !in_array($l->code, $request->languages) && $clang) {
                    $clang->delete();
                }
            }
            if (!is_array($request->languages)) {
                \App\ChurchLanguage::where('church_id', $id)
                    ->delete();
            }

            //clean up organization relations
            $church_organizations = [];
            $church_organizations_raw = \App\Organization::all();
            foreach ($church_organizations_raw as $c_org) {
                $church_organizations[$c_org->id] = $c_org->id;
            }
            if (is_array($request->church_organizations)) {
                foreach ($request->church_organizations as $c_org) {
                    \App\ChurchOrganization::addToOrganization($id, $c_org);
                    unset($church_organizations[$c_org]);
                }
            }
            foreach ($church_organizations as $c_org) {
                \App\ChurchOrganization::removeFromOrganization($id, $c_org);
            }

            // redirect
            $request->session()->flash('message', 'Church record <em>' . $church->id . '</em> saved!');
            return \Redirect::to('/admin/church');
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

    public function editChurchTags($id)
    {
        $church = \App\Church::findorfail($id);

        $data = [
            'church' => $church,
            'languages' => \App\Language::all(),
            'msg' => session('message'),
            'tag_page' => 'active',
        ];
        return view('admin.edit_church_tags', $data);
    }

    public function editChurchMeetingTimes($id)
    {
        $church = \App\Church::findorfail($id);

        $data = [
            'church' => $church,
            'languages' => \App\Language::all(),
            'msg' => session('message'),
            'meeting_page' => 'active',
        ];
        return view('admin.edit_church_meeting_times', $data);
    }
}
