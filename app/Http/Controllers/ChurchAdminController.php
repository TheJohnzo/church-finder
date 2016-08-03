<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ChurchAdminController extends Controller
{
    public function index()
    {
        $churches = \App\Church::paginate(20);
        $data = ['churches' => $churches];
        return view('admin/church', $data);
    }

    public function newChurch()
    {
        return 'new form';
    }

    public function editChurch()
    {
        return 'edit form';
    }
}
