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
        $data = [];
        return view('admin/new_organization', $data);
    }

    public function insertOrganization(Request $request)
    {
        $rules = array(
            'name'          => 'required',
            'national_url'  => 'url',
            'global_url'    => 'url',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/organization/new/')
                ->withInput()
                ->withErrors($validator);
        } else {
            $organization = \App\Organization::create();
            $organization->name = $request->name;
            $organization->size_in_churches = $request->size_in_churches;
            $organization->national_url = $request->national_url;
            $organization->global_url = $request->global_url;
            $organization->save();
            // redirect
            $request->session()->flash('message', 'Organization record <em>' . $organization->name . '</em> saved!');
            return \Redirect::to('/admin/organization');
        }
        return view('admin/new_organization');
    }

    public function editOrganization($id)
    {
        $organization = \App\Organization::find($id);
        $data = [
            'organization' =>  $organization
        ];
        return view('admin/edit_organization', $data);
    }

    public function updateOrganization($id, Request $request)
    {
        $rules = array(
            'name'          => 'required',
            'national_url'  => 'url',
            'global_url'    => 'url',
        );
        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to('admin/organization/edit/' . $id)
                ->withInput()
                ->withErrors($validator);
        } else {
            $organization = \App\Organization::find($id);
            $organization->name = $request->name;
            $organization->size_in_churches = $request->size_in_churches;
            $organization->national_url = $request->national_url;
            $organization->global_url = $request->global_url;
            $organization->save();
            // redirect
            $request->session()->flash('message', 'Organization record <em>' . $organization->name . '</em> saved!');
            return \Redirect::to('/admin/organization');
        }
    }

}
