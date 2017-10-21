@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div align="center"><h3>{!! $msg !!}</h3></div>
            <div class="panel panel-default">
                <div class="panel-heading">Organization Admin - {{ $org_count }} records found</div>
                <div class="panel-body">
                    <div style="width: 100%; padding: 10px; background-color: #f2f2f2;" align="center">
                        <form action="/{{ Request::path() }}" method="GET">
                        <input type="hidden" name="sort" value="{{ $sort }}" />
                        <input type="hidden" name="dir" value="{{ $dir }}" />
                        Filter Organizations: <input type="text" name="search" value="{{ $search }}" />
                        <button type="submit" class="small_button btn btn-primary">Filter</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="/{{ Request::path() }}">Clear Filter</a>
                        </form>
                    </div>
                    <table border="1" class="data">
                    <thead><tr>
                        <td>ID<br />
                            @include('admin.datagrid_header', ['field'=>'id'])</td>
                        <td>Name<br />
                            @include('admin.datagrid_header', ['field'=>'name'])</td>
                        <td>Size<br />
                            @include('admin.datagrid_header', ['field'=>'size_in_churches'])</td>
                        <td>National URL<br />
                            @include('admin.datagrid_header', ['field'=>'national_url'])</td>
                        <td>Global URL<br />
                            @include('admin.datagrid_header', ['field'=>'global_url'])</td>
                        <td>Created At<br />
                            @include('admin.datagrid_header', ['field'=>'created_at'])</td>
                        <td>Updated At<br />
                            @include('admin.datagrid_header', ['field'=>'updated_at'])</td>
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
