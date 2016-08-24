<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests;

class ChurchAdminController extends Controller
{
    public function index()
    {
        $churches = \App\Church::paginate(20);
        $data = [
            'churches' => $churches,
            'msg' => session('message'),
            'sizes' => \App\ChurchSize::all(),
        ];
        return view('admin/church', $data);
    }

    public function newChurch()
    {
        $data = [
            'sizes' => \App\ChurchSize::all(),
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
        $organizations = \App\Organization::all();
        $church_organizations_raw = \App\ChurchOrganization::where('church_id', '=', $id)
            ->select('organization_id')
            ->get();
        $church_organizations = [];
        foreach ($church_organizations_raw as $c_org) {
            $church_organizations[] = $c_org['organization_id'];
        }

        $data = [
            'church' =>  $church,
            'organizations' => $organizations,
            'church_organizations' => $church_organizations,
            'sizes' => \App\ChurchSize::all(),
        ];
        return view('admin/edit_church', $data);
    }

    public function updateChurch($id, Request $request)
    {
        $rules = array(
            'name'          => 'required',
            'contact_email' => 'email',
            'url'           => 'url',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/church/edit/' . $id)
                ->withInput()
                ->withErrors($validator);
        } else {
            $church = \App\Church::find($id);
            $church->name = $request->name;
            $church->size_in_people = $request->size_in_people;
            $church->url = $request->url;
            $church->contact_phone = $request->contact_phone;
            $church->contact_email = $request->contact_email;
            $church->save();

            //clean up organization relations
            $church_organizations = [];
            $church_organizations_raw = \App\Organization::all();
            foreach ($church_organizations_raw as $c_org) {
                $church_organizations[$c_org->id] = $c_org->id;
            }
            foreach ($request->church_organizations as $c_org) {
                \App\ChurchOrganization::addToOrganization($id, $c_org);
                unset($church_organizations[$c_org]);
            }
            foreach ($church_organizations as $c_org) {
                \App\ChurchOrganization::removeFromOrganization($id, $c_org);
            }

            // redirect
            $request->session()->flash('message', 'Church record <em>' . $church->name . '</em> saved!');
            return \Redirect::to('/admin/church');
        }
    }

}
