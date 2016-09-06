@extends('layouts.app')

@section('content')
<script>
    function saveTag(tag_id, box_id)
    {
        $("tr[data-tagid='" + tag_id + "']").addClass('alert-save');
        if ($("#tag_" + box_id).is(":checked")) {
            $("tr[data-tagid='" + tag_id + "']").children().children().prop('checked', true);
        } else {
            $("tr[data-tagid='" + tag_id + "']").children().children().prop('checked', false);
        }

        $.ajax({
            type: "POST",
            url: '/admin/church/{{ $id }}/tag/save',
            data: {
                tagid: tag_id, 
                _token: '{{ csrf_token() }}',
                checked: $("#tag_" + box_id).is(":checked")
            },
        }).done(function( data ) {
            $('#debug').html(data);
            $.each(jQuery.parseJSON(data), function( index, value ) {
                //process?
            });
        }).fail(function(xhr, status, error) {
            $('#debug').html(xhr.responseText);
        });
        $("tr[data-tagid='" + tag_id + "']").delay(500).fadeOut().fadeIn('slow');
        setTimeout(function() {
            $("tr[data-tagid='" + tag_id + "']").removeClass('alert-save');
        }, 800);
        
    }
</script>
<div id="debug"></div>
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            <h4>Church Tags</h4>
            @include('admin.church_menu')
            <div class="panel panel-default tab_div">
                <div class="panel-body">
                    <div style="width: 100%; padding: 10px; background-color: #f2f2f2;" align="center">
                        <form action="{{ url('/admin/church/' . $id . '/tag') }}" method="POST">
                        {{ Form::token() }}
                        <p>Only Show Language:  @include('admin.language_picker')</p>
                        <p>Filter Tags: <input type="text" name="search" value="{{ $search }}" /></p>
                        <button type="submit" class="small_button btn btn-primary">Filter</button>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="{{ url('/admin/church/' . $id . '/tag') }}">Clear Filter</a>
                        </form>
                    </div>
                    @forelse ($tags as $key => $tag)
                        @if ((!$twocolumn && in_array($key, [0,count($tags)])) || ($twocolumn && in_array($key, [0,count($tags)/2])))
                        <table border="1" class="data" style="width: {{ !$twocolumn ? '100%' : '50%' }}; {{ $twocolumn && $key==0 ? 'float: left;' : '' }};">
                        <thead><tr>
                            <td>Tagged</td>
                            <td>Tag </td>
                            <td>Language </td>
                        </thead></tr>
                        @endif
                            <tr data-tagid="{{ $tag->tag_id }}">
                                <td><input onclick="saveTag({{ $tag->tag_id }}, {{ $tag->id }})" type="checkbox" 
                                    id="tag_{{ $tag->id }}" {{ ($tag->tagged) ? 'checked' : '' }}/>
                                    &nbsp;&nbsp;<em style="font-size: 10px;">({{ $tag->tag_id }})</em></td>
                                <td>{{ $tag->tag }} </td>
                                <td><span class="flag-icon flag-icon-{{ $tag->primary_country }}" 
                                    style="background-size: contain;background-position: 50%;background-repeat: no-repeat;"></span>
                                </td>
                            </tr>
                        @if ($twocolumn && in_array($key, [count($tags)/2-1]))
                        </table>
                        @endif
                    @empty
                    <table class="data">
                        <thead><tr>
                            <td>Tagged</td>
                            <td>Tag </td>
                            <td>Language </td>
                        </thead></tr>
                        <tr><td colspan="3">No tags founds {{ ($search) ? 'with that search' : 'for this church' }}</td></tr>
                    @endforelse
                    </table>
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
