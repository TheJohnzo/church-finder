@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div align="center"><h3>{!! $msg !!}</h3></div>
            <div class="panel panel-default">
                <div class="panel-heading">Church Admin</div>

                <div class="panel-body">
                    <table border="1" style="width: 100%;">
                    <thead><tr>
                        <td>ID</td>
                        <td>Name</td>
                        <td>Size</td>
                        <td>URL</td>
                        <td>Contact Phone</td>
                        <td>Contact Email</td>
                        <td>Created At</td>
                        <td>Updated At</td>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No churches found in the database...</td>
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