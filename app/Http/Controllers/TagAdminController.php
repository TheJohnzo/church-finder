<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use App\Http\Requests;

class TagAdminController extends Controller
{
    public function index()
    {
        $tags = \App\TagTranslation::paginate(20);
        $data = [
            'tags' => $tags,
            'languages' => \App\Language::allIndexByCode(),
            'msg' => session('message'),
        ];
        return view('admin/tags', $data);
    }

    public function newTag()
    {
        $data = [
            'msg' => session('message'),
            'languages' => \App\Language::all(),
        ];
        return view('admin/tag_new', $data);
    }

    public function insertTag(Request $request)
    {
        $tag = new \App\tag;
        $tag->save();
        $fail_redirect = 'admin/tag/new';

        return $this->postSave($tag, $fail_redirect, $request);
    }

    public function editTag($id)
    {
        $tags = \App\TagTranslation::allByTagIdIndexByLanguage($id);

        $data = [
            'id' => $id,
            'tags' => $tags,
            'languages' => \App\Language::all(),
            'msg' => session('message'),
        ];
        return view('admin/tag_edit', $data);
    }

    public function updateTag($id, Request $request)
    {
        $tag = \App\tag::findorfail($id);
        $fail_redirect = 'admin/tag/edit/' . $id;

        return $this->postSave($tag, $fail_redirect, $request);
    }

    protected function postSave($tag, $fail_redirect, $request)
    {
        $rules = [];
        $languages = \App\Language::all();
        foreach ($languages as $l) {
            $rules['tag_' . $l->code] = 'required';
        }

        $validator = \Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \Redirect::to($fail_redirect)
                ->withInput()
                ->withErrors($validator);
        } else {

            foreach ($languages as $l) {
                $trans = \App\TagTranslation::where('tag_id', $tag->id)
                    ->where('language', $l->code)
                    ->first();
                if (!$trans) {
                    $trans = new \App\TagTranslation;
                    $trans->tag_id = $tag->id;
                }
                $field = 'tag_' . $l->code;
                $trans->tag = $request->$field;
                $trans->language = $l->code;
                $trans->save();
            }

            // redirect
            $request->session()->flash('message', 'New Tag saved!');
            return \Redirect::to('admin/tag');
        }
    }

    public function deleteTag($id, Request $request)
    {
        $tag = \App\tag::findorfail($id);

        \App\ChurchTag::where('tag_id', $id)
            ->delete();

        \App\TagTranslation::where('tag_id', $id)
            ->delete();

        $tag->delete();

        // redirect
        $request->session()->flash('message', 'Tag #' . $id . ' saved!');
        return \Redirect::to('admin/tag');
    }

}
