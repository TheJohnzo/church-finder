@extends('layouts.app')

@section('content')
<script>
    function deleteTag(id) {
        $("tr[data-tagid='" + id + "']").addClass('alert-danger');
        if (confirm('Are you sure you want to delete all tags for tag #' + id + '?')) {
            window.location = '/admin/tag/delete/' + id;
        }
        $("tr[data-tagid='" + id + "']").removeClass('alert-danger');
    }
</script>
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div align="center"><h3>{!! $msg !!}</h3></div>
            <div class="panel panel-default">
                <div class="panel-heading">Tag Admin - {{ $tags->count() }} records found</div>
                <div class="panel-body">
                    <div style="width: 100%; padding: 10px; background-color: #f2f2f2;" align="center">
                        <form action="/{{ Request::path() }}" method="GET">
                        <input type="hidden" name="sort" value="{{ $sort }}" />
                        <input type="hidden" name="dir" value="{{ $dir }}" />
                        Filter Tags: <input type="text" name="search" value="{{ $search }}" />
                        <button type="submit" class="small_button btn btn-primary">Filter</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="/{{ Request::path() }}">Clear Filter</a>
                        </form>
                    </div>
                    <table border="1" class="data">
                    <thead><tr>
                        <td>Tag #<br />
                            @include('admin.datagrid_header', ['field'=>'tag_id'])</td>
                        <td>Tag Name<br />
                            @include('admin.datagrid_header', ['field'=>'tag'])</td>
                        <td>Language<br />
                            @include('admin.datagrid_header', ['field'=>'language'])</td>
                        <td>Created At<br />
                            @include('admin.datagrid_header', ['field'=>'created_at'])</td>
                        <td>Updated At<br />
                            @include('admin.datagrid_header', ['field'=>'updated_at'])</td>
                        <td>Delete</td>
                    </thead></tr>
                    @forelse ($tags as $key => $tag)
                        <tr data-tagid="{{ $tag['tag_id'] }}">
                            <td>{{ $tag['tag_id'] }}</td>
                            <td><a href="{{ url('/admin/tag/edit/') . '/' . $tag['tag_id'] }}">{{ $tag['tag'] }}</a></td>
                            <td><span class="flag-icon flag-icon-{{ $languages[$tag['language']]['primary_country'] }}" 
                            style="background-size: contain;background-position: 50%;background-repeat: no-repeat;"></span></td>
                            <td>{{ $tag['created_at'] }}</td>
                            <td>{{ $tag['updated_at'] }}</td>
                            <td><a href="#" onClick="deleteTag({{ $tag['tag_id'] }})">Delete</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">No tags found in the database...</td>
                        </tr>
                    @endforelse
                    </table>
                    {!! $tags->render() !!}
                    <form action="{{ url('/admin/tag/new') }}" method="GET" class="toppadding25">
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
