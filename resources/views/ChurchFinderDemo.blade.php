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
            document.getElementById('mapbox_' + last_click).style.backgroundColor = '#FFFFFF';
        }

        var cur_marker = window['marker_' + id];
        map_0.setZoom({!! $params['zoom'] !!});
        map_0.panTo(cur_marker.position);
        document.getElementById('mapbox_' + id).style.backgroundColor = '#DDDDDD';
        last_click = id;
    }
</script>

    <div align="center" style="width: 99%; padding-top: 15px; padding-bottom: 25px;">

        <div>
        <form method="get">
            <em style="font-size: 12px;">&nbsp; within &nbsp;</em>
            {{ Form::select('distance', [5=>5, 10=>10, 20=>20, 40=>40, 100=>100], $distance) }}
            <em style="font-size: 12px;">&nbsp; of &nbsp;</em>
            <input name="search" value ="{{ $search }}"/> 
            <input type="submit" value="search" />
            &nbsp;<a href="{{ URL::to('map') }}">See All</a>
        </form>
        </div>

        <h3>{{ $msg }}</h3>

        <div style="padding-top: 10px; margin-left: 15px; margin-top: 0px; height: 400px; width: 29%; float: left; overflow: scroll; background-color: #AAAAAA">
            @forelse ($locations as $key => $location)
                <p style="background-color: #FFFFFF; padding: 5px; border: 1px solid black; width: 90%; min-height: 55px; text-align: left;" id="mapbox_{!! $key !!}">
                    <strong>{{ $location->name }}</strong> <br />
                    {{ $location->addr }}
                    <a href="javascript:infowindow_{!! $key !!}.open(map_0, marker_{!! $key !!});highlightMapBox({!! $key !!});">Show</a>
                    <a href="/church/{{ $location->church_id }}">Info</a>
                    @if (isset($location->distance))
                    <br />Distance: {{ round($location->distance, 2) }} km
                    @endif
                </p>
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

