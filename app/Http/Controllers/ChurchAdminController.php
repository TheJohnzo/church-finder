<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Cornford\Googlmapper\Facades\MapperFacade as Mapper;
use App\Http\Requests;

class ChurchAdminController extends Controller
{
    public function index(Request $request)
    {
        $churches = \App\Church::select('church.*', 'church_info.name')
            ->join('church_info', 'church.id', 'church_info.church_id')
            ->where('language', 'en');//default only filter by language #FIXME only english during dev
        if ($request->search) {
            $churches->where(function ($q) use ($request) {
                $q->whereRaw('LOWER(name) LIKE \'%' . strtolower($request->search) . '%\'');
                $q->orWhereRaw('LOWER(url) LIKE \'%' . strtolower($request->search) . '%\'');
                $q->orWhereRaw('LOWER(contact_phone) LIKE \'%' . strtolower($request->search) . '%\'');
                $q->orWhereRaw('LOWER(contact_email) LIKE \'%' . strtolower($request->search) . '%\'');
            });
        }
        if ($request->sort && $request->dir) {
            $churches->orderBy($request->sort, $request->dir);
        }
        $data = [
            'churches' => $churches->paginate(20),
            'msg' => session('message'),
            'sizes' => \App\ChurchSize::all(),
            'missing_info' => \App\Church::allMissingInfo(),
            'missing_address' => \App\Church::allMissingAddress(),
            'missing_meeting_time' => \App\Church::allMissingMeetingTime(),
            'missing_contact' => \App\Church::allMissingContact(),
            //for data grid
            'sort' => $request->sort,
            'dir' => $request->dir,
            'search' => $request->search
        ];
        return view('admin/church', $data);
    }

    public function newChurch()
    {
        $data = [
            'sizes' => \App\ChurchSize::all(),
            'languages' => \App\Language::all(),
            'organizations' => \App\Organization::allIndexById(),
            'sizes' => \App\ChurchSize::allIndexBySize(),
        ];
        return view('admin/church_new', $data);
    }

    public function insertChurch(Request $request)
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
            return \Redirect::to('admin/church/new/')
                ->withInput()
                ->withErrors($validator);
        } else {
            $church = \App\Church::create();
            $church->size_in_people = $request->size_in_people;
            $church->url = $request->url;
            $church->contact_phone = $request->contact_phone;
            $church->contact_email = $request->contact_email;
            $church->save();

            $this->postSave($church->id, $languages, $request);

            // redirect
            $request->session()->flash('message', 'Church record <em>' . $church->id . '</em> saved!');
            return \Redirect::to('/admin/church');
        }
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
            'organizations' => \App\Organization::allIndexById(),
            'church_organizations' => $church_organizations,
            'sizes' => \App\ChurchSize::allIndexBySize(),
            'languages' => \App\Language::all(),
            'addresses' => \App\ChurchAddress::where('church_id', $id)->get(),
            'address_labels' => \App\ChurchAddressLabel::allByChurchId($id),
            'selected_languages' => \App\ChurchLanguage::allIndexByLanguage($id),
            'msg' => session('message'),
            'church_page' => 'active',
        ];
        return view('admin/church_edit', $data);
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

            $this->postSave($id, $languages, $request);

            // redirect
            $request->session()->flash('message', 'Church record <em>' . $church->id . '</em> saved!');
            return \Redirect::to('/admin/church');
        }
    }

    /**
     * Post save logic used by both isnert and update
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

    public function editChurchTags($id, Request $request)
    {
        $church = \App\Church::findorfail($id);
        $tags = \App\TagTranslation::allWithChurch($id, $request);
        $selected_languages = [];
        if (is_array($request->languages)) {
            foreach ($request->languages as $lang) {
                $selected_languages[$lang] = 'checked';
            }
        }
        $data = [
            'id' => $id,
            'search' => $request->search,
            'tags' => $tags,
            'twocolumn' => (count($tags) > 20) ? true : false,
            'church' => $church,
            'languages' => \App\Language::all(),
            'selected_languages' => $selected_languages,
            'msg' => session('message'),
            'tag_page' => 'active',
        ];
        return view('admin.church_tags', $data);
    }

    public function saveChurchTags($id, Request $request)
    {
        $church_tag = \App\ChurchTag::where('church_id', $id)
            ->where('tag_id', $request->tagid)
            ->first();
        $saved = true;

        if ($request->checked === 'true' && !$church_tag) {
            $church_tag = new \App\ChurchTag;
            $church_tag->church_id = $id;
            $church_tag->tag_id = $request->tagid;
            $church_tag->save();
        }

        if ($request->checked === 'false' && $church_tag) {
            $church_tag->delete();
            $saved = false;
        }

        $data = ['saved' => $saved, 'checked' => $request->checked];
        return json_encode($data);
    }

}
