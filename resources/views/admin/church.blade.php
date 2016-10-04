@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div align="center"><h3>{!! $msg !!}</h3></div>
            <div class="panel panel-default">
                <div class="panel-heading">Church Admin - {{ $churches->count() }} records found</div>
                <div class="panel-body">
                    <div style="width: 100%; padding: 10px; background-color: #f2f2f2;" align="center">
                        <form action="/{{ Request::path() }}" method="GET">
                        <input type="hidden" name="sort" value="{{ $sort }}" />
                        <input type="hidden" name="dir" value="{{ $dir }}" />
                        Filter Churches: <input type="text" name="search" value="{{ $search }}" />
                        <button type="submit" class="small_button btn btn-primary">Filter</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="/{{ Request::path() }}">Clear Filter</a>
                        </form>
                    </div>
                    <div align="right">
                        {!! FA::icon('info-circle') !!} = Name, 
                        {!! FA::icon('map-marker') !!} = Address, 
                        {!! FA::icon('clock-o') !!} = Meeting Time, 
                        {!! FA::icon('phone') !!} = Contact Info
                    </div>
                    <table border="1" class="data">
                    <thead><tr>
                        <td>ID<br />
                            @include('admin.datagrid_header', ['field'=>'id'])</td>
                        <td>Name<br />
                            @include('admin.datagrid_header', ['field'=>'name'])</td>
                        <td>Size<br />
                            @include('admin.datagrid_header', ['field'=>'size_in_people'])</td>
                        <td>URL<br />
                            @include('admin.datagrid_header', ['field'=>'url'])</td>
                        <td>Contact Phone<br />
                            @include('admin.datagrid_header', ['field'=>'contact_phone'])</td>
                        <td>Contact Email<br />
                            @include('admin.datagrid_header', ['field'=>'contact_email'])</td>
                        <td>Created At<br />
                            @include('admin.datagrid_header', ['field'=>'created_at'])</td>
                        <td>Updated At<br />
                            @include('admin.datagrid_header', ['field'=>'updated_at'])</td>
                        <td>Missing Data</td>
                    </thead></tr>
                    @forelse ($churches as $key => $church)
                        <tr>
                            <td>{{ $church['id'] }}</td>
                            <td><a href="{{ url('/admin/church/edit') }}/{{ $church['id'] }}">{{ $church['name'] }}</a></td>
                            <td>{{ $church['size_in_people'] }}</td>
                            <td>{{ $church['url'] }}</td>
                            <td>{{ $church['contact_phone'] }}</td>
                            <td>{{ $church['contact_email'] }}</td>
                            <td>{{ $church['created_at'] }}</td>
                            <td>{{ $church['updated_at'] }}</td>
                            <td style="font-size: 18px;">
                                <a href="{{ url('/admin/church') }}/{{ $church['id'] }}" class="red_icon">
                                    {!! (isset($missing_info[$church['id']])) ? FA::icon('info-circle') : '' !!}
                                </a>
                                <a href="{{ url('/admin/church') }}/{{ $church['id'] }}/address" class="red_icon">
                                    {!! (isset($missing_address[$church['id']])) ? FA::icon('map-marker') : '' !!}
                                </a>
                                <a href="{{ url('/admin/church') }}/{{ $church['id'] }}/meetingtime" class="orange_icon">
                                    {!! (isset($missing_meeting_time[$church['id']])) ? FA::icon('clock-o') : '' !!}
                                </a>
                                <a href="{{ url('/admin/church/edit') }}/{{ $church['id'] }}" class="orange_icon">
                                    {!! (isset($missing_contact[$church['id']])) ? FA::icon('phone') : '' !!}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">No churches found in the database...</td>
                        </tr>
                    @endforelse
                    </table>
                    {!! $churches->render() !!}
                    <form action="{{ url('/admin/church/new') }}" method="GET" class="toppadding25">
                        <button type="submit" class="btn btn-primary">
                            {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Create New
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
