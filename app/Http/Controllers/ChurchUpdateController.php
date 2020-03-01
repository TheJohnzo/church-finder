<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use App\Http\Requests;

/**
 * Class ChurchAdminController
 * @package App\Http\Controllers
 *
 * A streamlined way to get updated church data directly from churches,
 *  to be exposed via emailed links.
 *
 */
class ChurchUpdateController extends AdminController
{

    public function editChurch($id, Request $request)
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
            'chosen_lang' => $request->lang ?? 'ja',
            'church' => $church,
            'infos' => \App\ChurchInfo::allByChurchIdIndexed($id),
            'organizations' => \App\Organization::allIndexById(),
            'church_organizations' => $church_organizations,
            'sizes' => \App\ChurchSize::allIndexBySize(),
            'languages' => \App\Language::all(),
            'addresses' => \App\ChurchAddress::where('church_id', $id)->get(),
            'address_labels' => \App\ChurchAddressLabel::allByChurchId($id),
            'selected_languages' => \App\ChurchLanguage::allIndexByLanguage($id),
            'msg' => session('message')
        ];
        return view('update/church_edit', $data);
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
            return \Redirect::to('update/church/edit/' . $id)
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

            $this->postSave($id, $languages, $request);

            // redirect
            $request->session()->flash('message', 'Church record <em>' . $church->id . '</em> saved!');
            return \Redirect::to('/update/church');
        }
    }

    // we email magic URLs to churches and give them this link to update just their church
    public function updateChurchFromUrl($id, Request $request)
    {
        $rules = array(
            'contact_email' => 'email',
            'url'           => 'url',
        );

        $validator = \Validator::make($request->all(), $rules);
        $languages = \App\Language::query()
            ->where('code', $request->current_language)
            ->get();
        if ($validator->fails()) {
            return \Redirect::to('update/church/edit/' . $id)
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

            $this->postSave($id, $languages, $request);

            // redirect
            $request->session()->flash('message', 'Church record <em>' . $church->id . '</em> saved!');
            if ($request->address_correct == 1) {
                return view('update/arigatou?lang=' . $request->current_language);
            } else {
                return \Redirect::to('/updatefromurl/address/' . $id . '?lang=' . $request->current_language);
            }
        }
    }

    # The address wasn't correct to have them create a new one
    public function updateChurchAddressFromUrl($id, Request $request) {
        $church = \App\Church::findorfail($id);

        //fake new addr allows the view to display a "create new" form after all the edit forms
        $fake_new_addr = new \App\ChurchAddress;
        $fake_new_addr->id = 'new';
        $addresses[] = $fake_new_addr;

        $data = [
            'chosen_lang' => $request->lang ?? 'ja',
            'church' => $church,
            'languages' => \App\Language::all(),
            'addresses' => $addresses,
            'address_labels' => \App\ChurchAddressLabel::allByChurchId($id),
            'msg' => session('message'),
            'address_id' => 0,
            'lang' => $request->lang,
        ];

        if ($request->isMethod('post')) {
            $data['address_labels'] = \App\ChurchAddress::lookupAddresses($request->search_0);
        }
# TODO this is not the array format we exepct and it's breaking the page
die(var_export($data['address_labels'][31], true));
        return view('update/address_edit', $data);
    }

    public function updateChurchAddressFromUrlSave($id, Request $request) {
        $church = \App\Church::findorfail($id);
        # TODO we need to fix this save logic for the end-user church address edit
        //        $addr = \App\ChurchAddress::findorfail($address_id);
        //FIX ME
//        $addr
//        AddressAdminController::postSave($id, $addr, $request);
    }

    /**
     * Post save logic used by both insert and update
     */
    protected function postSave($id, $languages, $request)
    {

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

            //update languages spoken at church
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
                if ($c_org > 0) {
                    \App\ChurchOrganization::addToOrganization($id, $c_org);
                    unset($church_organizations[$c_org]);
                }
            }
        }
        foreach ($church_organizations as $c_org) {
            \App\ChurchOrganization::removeFromOrganization($id, $c_org);
        }
    }

}
