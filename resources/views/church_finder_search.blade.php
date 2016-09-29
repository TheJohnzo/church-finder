@extends('layouts.map')

@section('content')
    <div align="center" style="width: 100%; padding-top: 15px; padding-bottom: 25px;">

        <div style="float:right; padding-right: 15px;"><a href="{{ URL::to('map') . '?lang=' . $lang }}">@lang('messages.with-map') {!! FA::icon('hand-o-right') !!}</a></div>

        <div>
        <form method="get" action="{{ URL::to('search') }}">
            <em style="font-size: 12px;">&nbsp; @lang('messages.within') &nbsp;</em>
            {{ Form::select('distance', [5=>5, 10=>10, 20=>20, 40=>40, 100=>100], $distance) }}
            <em style="font-size: 12px;">&nbsp; @lang('messages.of') &nbsp;</em>
            <input name="lang" type="hidden" value ="{{ $lang }}"/>
            <input name="search" type="text" value ="{{ $search }}" placeholder="@lang('messages.city')"/> 
            <input type="submit" value="@lang('messages.search')" />
            &nbsp;<a href="{{ URL::to('search') . '?lang=' . $lang }}">@lang('messages.see-all')</a>
            <br />
            <div style="width: 75%">
            @foreach ($tag_translations as $t)
                <input type="checkbox" name="tags[]" value="{{ $t->tag_id }}" 
                @if (is_array($tags) && in_array($t->tag_id, $tags))
                    checked
                @endif
                />&nbsp;{{ $t->tag }} &nbsp;&nbsp;
            @endforeach
            </div>
        </form>
        </div>

        <h3>@choice('messages.displaying-churches', count($locations) ,['number'=>count($locations)])</h3>

        <div class="search_results search_results_no_map">
            @forelse ($locations as $key => $location)
                <a name="church_{{ $key }}"></a>
                <div class="church_box {{ ($key % 2 == 0) ? 'left_box' : 'right_box' }}" id="mapbox_{!! $key !!}">
                    <div class="record-number">#{{ $key+1 }}</div>
                    <h4>{{ $location->name }}</h4>
                    <h5>{{ $location->addr }}</h5>
                    @if ($location->url)
                        <h5>{!! FA::icon('link') !!} <a href="{{ $location->url }}" target="_blank">{{ $location->url }}</a></h5>
                    @endif
                    <a href="{{ URL::to('/church/' . $location->church_id . '/?lang=' . $lang) }}">{!! FA::icon('info-circle') !!}  @lang('messages.see-more')</a>
                    @if (isset($location->distance))
                    <br />@lang('messages.distance'): {{ round($location->distance, 2) }} km
                    @endif
                    <br />
                    @foreach ($location->tag as $tag)
                        @foreach ($tag->translation->all() as $t)
                            @if ($t->language == $lang)
                                <a href="{{ URL::to('search') . '?lang=' . $lang . '&search=' . $search . '&distance=' . $distance . '&tags[]=' . $t->tag_id }}">#{{ $t->tag }}</a>, 
                            @endif
                        @endforeach
                    @endforeach
                </div>
            @empty
                <div class="church_box">
                    <h4>No Churches found...</h4>
                </div>
            @endforelse
        </div>

    </div>
@endsection

