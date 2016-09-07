@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div align="center"><h3>{!! $msg !!}</h3></div>
            <div class="panel panel-default">
                <div class="panel-heading">Church Admin</div>
                <div class="panel-body">
                    <div align="right">
                        {!! FA::icon('info-circle') !!} = Name, 
                        {!! FA::icon('map-marker') !!} = Address, 
                        {!! FA::icon('clock-o') !!} = Meeting Time, 
                        {!! FA::icon('phone') !!} = Contact Info
                    </div>
                    <table border="1" class="data">
                    <thead><tr>
                        <td>ID</td>
                        <td>Name</td>
                        <td>Size</td>
                        <td>URL</td>
                        <td>Contact Phone</td>
                        <td>Contact Email</td>
                        <td>Created At</td>
                        <td>Updated At</td>
                        <td>Missing Data</td>
                    </thead></tr>
                    @forelse ($churches as $key => $church)
                        <tr>
                            <td>{{ $church['id'] }}</td>
                            <td><a href="{{ url('/admin/church/edit') }}/{{ $church['id'] }}">{{ $churchInfo[$church['id']]['name'] }}</a></td>
                            <td>{{ $church['size_in_people'] }}</td>
                            <td>{{ $church['url'] }}</td>
                            <td>{{ $church['contact_phone'] }}</td>
                            <td>{{ $church['contact_email'] }}</td>
                            <td>{{ $church['created_at'] }}</td>
                            <td>{{ $church['updated_at'] }}</td>
                            <td style="font-size: 18px;"><span style="color: red;">
                                {!! (isset($missing_info[$church['id']])) ? FA::icon('info-circle') : '' !!}
                                {!! (isset($missing_address[$church['id']])) ? FA::icon('map-marker') : '' !!}
                            </span><span style="color: orange;">
                                {!! (isset($missing_meeting_time[$church['id']])) ? FA::icon('clock-o') : '' !!}
                                {!! (isset($missing_contact[$church['id']])) ? FA::icon('phone') : '' !!}
                            </span></td>
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
