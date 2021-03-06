<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests;

class OrganizationAdminController extends AdminController
{
    public function index(Request $request)
    {
        $organizations = \App\Organization::select('*')            
            ->join('organization_info', 'organization.id', 'organization_info.organization_id')
            ->where('language', 'ja');//default only filter by language #FIXME only english during dev;

        if ($request->search) {
            $organizations->where(function ($q) use ($request) {
                $q->whereRaw('LOWER(organization_info.name) LIKE \'%' . strtolower($request->search) . '%\'');
                $q->orWhereRaw('LOWER(national_url) LIKE \'%' . strtolower($request->search) . '%\'');
                $q->orWhereRaw('LOWER(global_url) LIKE \'%' . strtolower($request->search) . '%\'');
            });
        }
        if ($request->sort && $request->dir) {
            $organizations->orderBy($request->sort, $request->dir);
        }
        $data = [
            'organizations' => $organizations->paginate(20),
            'org_count' => $organizations->count(),
            'msg' => session('message'),
            //for data grid
            'sort' => $request->sort,
            'dir' => $request->dir,
            'search' => $request->search
        ];
        return view('admin/organization', $data);
    }

/**
 * TODO fix organization are to store "name" in Info table
 */
    public function newOrganization()
    {
        $data = [
            'countries' => \App\Country::allIndexById(),
            'languages' => \App\Language::allIndexByCode(),
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
            'active_countries' => \App\OrganizationCountry::allByOrgIdCountryIdOnly($id),
            'infos' => \App\OrganizationInfo::allByOrgIdIndexByLanguage($id),
            'languages' => \App\Language::allIndexByCode(),
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
            'national_url'  => 'url',
            'global_url'    => 'url',
        );
        $languages = \App\Language::allIndexByCode();
        foreach ($languages as $lang) {
            $rules['name_' . $lang['code']] = 'required';
            $rules['description_' . $lang['code']] = 'required';
        }
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/organization/edit/' . $organization->id)
                ->withInput()
                ->withErrors($validator);
        } else {
            $organization->size_in_churches = $request->size_in_churches;
            $organization->national_url = $request->national_url;
            $organization->global_url = $request->global_url;
            $organization->save();

            foreach ($languages as $lang) {
                $info = \App\OrganizationInfo::where('organization_id', $organization->id)
                    ->where('language', $lang['code'])
                    ->first();
                if (!$info) {
                    $info = new \App\OrganizationInfo;
                    $info->organization_id = $organization->id;
                    $info->language = $lang['code'];
                }
                $descField = 'description_' . $lang['code'];
                $info->description = $request->$descField;
                $nameField = 'name_' . $lang['code'];
                $info->name = $request->$nameField;
                $info->save();
            }

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
