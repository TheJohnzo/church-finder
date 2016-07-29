<script>
    var map_0;//hardcoded to support only one map per page for developement.
    var last_click = -1;//track previous click to help update UI with subsequent clicks

    @foreach ($locations as $key => $location)
        //declare these as globals so can reference in custom code
        var infowindow_{!! $key !!};
        var marker_{!! $key !!};
    @endforeach

    function highlightMapBox(id) {
        if (last_click >= 0 && id !== last_click) {
            var last_window = 'infowindow_' + last_click;
            window[last_window].close();
            document.getElementById('mapbox_' + last_click).style.backgroundColor = '#FFFFFF';
        }

        document.getElementById('mapbox_' + id).style.backgroundColor = '#DDDDDD';
        last_click = id;
    }
</script>

    <div align="center" style="background-color: #f2f2f2; width: 99%; padding-top: 15px; padding-bottom: 25px;">

        <div>
        <form method="get">
            
            <em style="font-size: 12px;">&nbsp; within &nbsp;</em>
            <select name="distance">
                <option value="5"  {{ $distance == 5   ? 'selected' : '' }}>5km</option>
                <option value="10" {{ $distance == 10  ? 'selected' : '' }}>10km</option>
                <option value="20" {{ $distance == 20  ? 'selected' : '' }}>20km</option>
                <option value="40" {{ $distance == 40  ? 'selected' : '' }}>40km</option>
                <option value="100"{{ $distance == 100 ? 'selected' : '' }}>100km</option>
            </select>
            <em style="font-size: 12px;">&nbsp; of &nbsp;</em>
            <input name="search" value ="{{ $search }}"/> 
            <input type="submit" value="search" />
            
        </form>
        </div>

        <h3>{{ $msg }}</h3>

        <div style="margin-left: 15px; margin-top: 0px; height: 400px; width: 30%; float: left; overflow: scroll; background-color: #AAAAAA">
            @forelse ($locations as $key => $location)
                <p style="background-color: #FFFFFF; padding: 5px; border: 1px solid black; width: 90%; min-height: 55px; text-align: left;" id="mapbox_{!! $key !!}">
                    <strong>{{ $location->name }}</strong> <br />
                    {{ $location->addr }}
                    <a href="javascript:infowindow_{!! $key !!}.open(map_0, marker_{!! $key !!});highlightMapBox({!! $key !!});">show</a>
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
    
        <div style="margin: 15px; height: 400px;">
        {!! Mapper::render() !!}
        </div>

    </div>
