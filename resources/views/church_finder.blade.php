@extends('layouts.map')

@section('content')
<script>
    var map_0;//hardcoded to support only one map per page for developement.
    var last_click = -1;//track previous click to help update UI with subsequent clicks

    //declare these as globals so can reference in custom code
    @foreach ($locations as $key => $location)
        var infowindow_{!! $key !!};
        var marker_{!! $key !!};
    @endforeach

    function highlightMapBox(id) {
        if (last_click >= 0 && id !== last_click) {
            var last_window = 'infowindow_' + last_click;
            window[last_window].close();
            document.getElementById('mapbox_' + last_click).className='church_box';
        }

        var cur_marker = window['marker_' + id];
        map_0.setZoom({!! $params['zoom'] !!});
        map_0.panTo(cur_marker.position);
        document.getElementById('mapbox_' + id).className='selected_church_box';
        window.location.href = "#church_" + id;
        last_click = id;
    }
</script>

    <div align="center" style="width: 100%; padding-top: 15px; padding-bottom: 25px;">

        <div>
        <form method="get" action="{{ URL::to('map') }}">
            <em style="font-size: 12px;">&nbsp; @lang('messages.within') &nbsp;</em>
            {{ Form::select('distance', [5=>5, 10=>10, 20=>20, 40=>40, 100=>100], $distance) }}
            <em style="font-size: 12px;">&nbsp; @lang('messages.of') &nbsp;</em>
            <input name="lang" type="hidden" value ="{{ $lang }}"/>
            <input name="search" type="text" value ="{{ $search }}"/> 
            <input type="submit" value="@lang('messages.search')" />
            &nbsp;<a href="{{ URL::to('map') . '?lang=' . $lang }}">@lang('messages.see-all')</a>
        </form>
        </div>

        <h3>{{ $msg }}</h3>

        <div style="padding-top: 10px; margin-top: 0px; height: 400px; width: 29%; float: left; overflow: scroll; background-color: #0F1F41">
            @forelse ($locations as $key => $location)
                <a name="church_{{ $key }}"></a>
                <div class="church_box" id="mapbox_{!! $key !!}">
                    <h4>{{ $location->name }}</h4>
                    <h5>{{ $location->addr }}</h5>
                    <a href="#church_{{ $key }}" 
                        onclick="infowindow_{!! $key !!}.open(map_0, marker_{!! $key !!});highlightMapBox({!! $key !!});">{!! FA::icon('map') !!}  @lang('messages.show')</a>
                    &nbsp;&nbsp; | &nbsp;&nbsp;
                    <a href="{{ URL::to('/church/' . $location->church_id . '/?lang=' . $lang) }}">{!! FA::icon('info-circle') !!}  @lang('messages.see-more')</a>
                    @if (isset($location->distance))
                    <br />@lang('messages.distance'): {{ round($location->distance, 2) }} km
                    @endif
                </div>
            @empty
                <p style="background-color: #FFFFFF; padding: 5px; border: 1px solid black; width: 90%; height: 55px; text-align: left;">
                    No Churches found...
                </p>
            @endforelse
        </div>
    
        <div style="height: 400px; width: 68%; background-color: #000000; float: right;">
        {!! Mapper::render() !!}
        </div>

    </div>
@endsection
