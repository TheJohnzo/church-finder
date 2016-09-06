@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div align="center"><h3>{!! $msg !!}</h3></div>
            <div class="panel panel-default">
                <div class="panel-heading">Organization Admin</div>

                <div class="panel-body">
                    <table border="1" class="data">
                    <thead><tr>
                        <td>ID</td>
                        <td>Name</td>
                        <td>Size</td>
                        <td>National URL</td>
                        <td>Global URL</td>
                        <td>Created At</td>
                        <td>Updated At</td>
                    </thead></tr>
                    @forelse ($organizations as $key => $org)
                        <tr>
                            <td>{{ $org['id'] }}</td>
                            <td><a href="{{ url('/admin/organization/edit') }}/{{ $org['id'] }}">{{ $org['name'] }}</a></td>
                            <td>{{ $org['size_in_churches'] }}</td>
                            <td>{{ $org['national_url'] }}</td>
                            <td>{{ $org['global_url'] }}</td>
                            <td>{{ $org['created_at'] }}</td>
                            <td>{{ $org['updated_at'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No organizations found in the database...</td>
                        </tr>
                    @endforelse
                    </table>
                    {!! $organizations->render() !!}
                    <form action="{{ url('/admin/organization/new') }}" method="GET" class="toppadding25">
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
