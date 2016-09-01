<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests;

class OrganizationAdminController extends Controller
{
    public function index()
    {
        $organizations = \App\Organization::paginate(20);
        $data = [
            'organizations' => $organizations,
            'msg' => session('message'),
        ];
        return view('admin/organization', $data);
    }

    public function newOrganization()
    {
        $data = [
            'countries' => \App\Country::allIndexById(),
        ];
        return view('admin/organization_new', $data);
    }

    public function insertOrganization(Request $request)
    {
        $organization = new \App\Organization;
        $fail_redirect = 'admin/organization/new';

        return $this->postSave($organization, $fail_redirect, $request);
    }

    public function editOrganization($id)
    {
        $organization = \App\Organization::find($id);
        $data = [
            'organization' => $organization,
            'countries' => \App\Country::allIndexById(),
            'active_countries' => \App\OrganizationCountry::allByOrgIdCountryIdOnly($id)
        ];
        return view('admin/organization_edit', $data);
    }

    public function updateOrganization($id, Request $request)
    {
        $organization = \App\Organization::find($id);
        $fail_redirect = 'admin/organization/edit/' . $id;

        return $this->postSave($organization, $fail_redirect, $request);
    }

    protected function postSave($organization, $fail_redirect, $request)
    {
        $rules = array(
            'name'          => 'required',
            'national_url'  => 'url',
            'global_url'    => 'url',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/organization/edit/' . $organization->id)
                ->withInput()
                ->withErrors($validator);
        } else {
            $organization->name = $request->name;
            $organization->size_in_churches = $request->size_in_churches;
            $organization->national_url = $request->national_url;
            $organization->global_url = $request->global_url;
            $organization->save();

            //clean up country relations
            $organization_countries = [];
            $organization_countries_raw = \App\Country::all();
            foreach ($organization_countries_raw as $o_co) {
                $organization_countries[$o_co->id] = $o_co->id;
            }
            if (is_array($request->countries)) {
                foreach ($request->countries as $c) {
                    if ($c > 0) {
                        \App\OrganizationCountry::addToCountry($organization->id, $c);
                        unset($organization_countries[$c]);
                    }
                }
            }
            foreach ($organization_countries as $o_c) {
                \App\OrganizationCountry::removeFromCountry($organization->id, $o_c);
            }

            // redirect
            $request->session()->flash('message', 'Organization record <em>' . $organization->name . '</em> saved!');
            return \Redirect::to('/admin/organization');
        }
    }

}
