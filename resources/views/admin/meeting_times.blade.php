@extends('layouts.app')

@section('content')
<script>
    function deleteMeeting(id) {
        $('#tr_' + id).addClass('alert-danger');
        if (confirm('Are you sure you want to delete meeting record ' + id + '?')) {
            window.location = '/admin/church/{{ $church->id }}/meetingtime/delete/' + id;
        }
        $('#tr_' + id).removeClass('alert-danger');
    }
</script>
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            <h4>Church Meeting Times</h4>
            @include('admin.church_menu')
            <div class="panel panel-default tab_div">
                <div class="panel-body">
                    <table border="1" class="data">
                    <thead><tr>
                        <td>ID</td>
                        <td>Day </td>
                        <td>Time </td>
                        <td>Address</td>
                        <td>Languages</td>
                        <td>Created At</td>
                        <td>Updated At</td>
                        <td>Delete</td>
                    </thead></tr>
                    @forelse ($meeting_times as $key => $time)
                        <tr id="tr_{{ $time['id'] }}">
                            <td>{{ $time['id'] }}</td>
                            <td><a href="{{ url('/admin/church') }}/{{ $church['id'] }}/meetingtime/edit/{{ $time['id'] }}">{{ $days[$time['day_of_week']] }}</a></td>
                            <td>{{ $time['time'] }}</td>
                            <td>{{ $addresses[$time['church_address_id']] }}</td>
                            <td>
                                @if (isset($meeting_time_languages[$time['id']]))
                                @foreach ($meeting_time_languages[$time['id']] as $lang)
                                    <span class="flag-icon flag-icon-{{ $languages[$lang->language]->primary_country }}" 
                                        style="background-size: contain;background-position: 50%;background-repeat: no-repeat;"></span>
                                        &nbsp;&nbsp;&nbsp;&nbsp;
                                @endforeach
                                @endif
                            </td>
                            <td>{{ $time['created_at'] }}</td>
                            <td>{{ $time['updated_at'] }}</td>
                            <td><a href="#" onclick="deleteMeeting({{ $time['id'] }})">Delete</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No meeting times found in the database for this church...</td>
                        </tr>
                    @endforelse
                    </table>
                    {!! $meeting_times->render() !!}
                    <form action="{{ url('/admin/church/' . $church->id . '/meetingtime/new') }}" method="GET" class="toppadding25">
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
