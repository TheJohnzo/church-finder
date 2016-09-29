@extends('layouts.map')

@section('content')
    <div align="left" style="padding: 25px;">
        <button type="submit" class="btn btn-primary" onclick="window.history.back();">
            {!! FA::icon('hand-o-left') !!} @lang('messages.back-to-map')
        </button>
        <div align="center" style="float: right;width: 75%">
            <ul class="nav nav-justified">
              <!-- <li><a href="#top">{!! FA::icon('arrow-circle-up') !!} Top</a></li> 
                  Consider hidding content sections until menu items clicked for easier viewing in iframe.  
                  -->
              @if (count($church->meetingtime()->get()) > 0)
              <li>
                  <a href="#servicetimes">{!! FA::icon('calendar') !!} @lang('messages.service-times') {!! FA::icon('arrow-circle-down') !!}</a>
              </li>
              @endif
              @if (count($church->organization()->get()) > 0)
              <li>
                <a href="#orgs">{!! FA::icon('group') !!} @lang('messages.related-orgs') {!! FA::icon('arrow-circle-down') !!}</a>
              </li>
              @endif
              @if (count($church->tag()->get()) > 0)
              <li>
                <a href="#tags">{!! FA::icon('tags') !!} @lang('messages.tags') {!! FA::icon('arrow-circle-down') !!}</a>
              </li>
              @endif
            </ul>
        </div>
    </div>
    <div class="blue detail_box">
        <a name="top"></a>
        <h2>@foreach ($church->info()->get() as $info)
             @if ($info->language == $lang)
                {{ $info->name }}
             @endif
            @endforeach
        </h2>
        <div align="center">
        <h3 class="centered">@foreach ($church->address()->where('primary', 1)->first()->label as $label)
             @if ($label->language == $lang)
                {{ $label->addr }}
             @endif
            @endforeach
        </h3>
        <h4 class="centered">@foreach ($church->info()->get() as $info)
             @if ($info->language == $lang)
                {{ $info->description }}
             @endif
            @endforeach
        </h4>
        </div>

        @if ($church->contact_phone)
            <h4>{!! FA::icon('phone') !!} {{ $church->contact_phone }}</h4>
        @endif

        @if ($church->contact_email)
            <h4>{!! FA::icon('envelope') !!} {{ $church->contact_email }}</h4>
        @endif

        @if ($church->url)
            <h4>{!! FA::icon('link') !!} <a href="{{ $church->url }}" target="_blank">{{ $church->url }}</a></h4>
        @endif
    </div>
    <div class="grey detail_box">
        <a name="servicetimes"></a>
        @if (count($church->meetingtime()->get()) > 0)
            <h3>@lang('messages.service-times')</h3>
            @foreach ($church->meetingtime()->get() as $time)
                <h4>@lang('messages.day_' . $time->day_of_week) @ {{ $time->time }}
                    @foreach ($time->language()->get() as $language)
                        <span class="flag-icon flag-icon-{{ $languages[$language->language]->primary_country }}" 
                            style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></span>
                    @endforeach
                </h4>
                    {{-- TODO should check here to see if service address is different than above --}}
                    @if ($time->address()->first()->primary !== 1)
                        @foreach ($time->address()->first()->label()->get() as $label)
                            @if ($lang == $label->language)
                                <h5>{{ $label->addr }}</h5>
                            @endif
                        @endforeach
                    @endif
            @endforeach
        @endif
    </div>
    <div class="blue detail_box">
        <a name="orgs"></a>
        @if (count($church->organization()->get()) > 0)
            <h3>@lang('messages.related-orgs')</h3>
            @foreach ($church->organization()->get() as $org)
                <h4>{!! FA::icon('link') !!} <a href="{{ URL::to('/org/' . $org->id) }}?lang={{ $lang }}">{{ $org->name }}</a></h4>
            @endforeach
        @endif
    </div>
    <div class="grey detail_box">
        <a name="tags"></a>
        @if (count($church->tag()->get()) > 0)
            <h4>
            @foreach ($church->tag as $tag)
                @foreach ($tag->translation->all() as $t)
                    @if ($t->language == $lang)
                        <a href="{{ URL::to('search') . '?lang=' . $lang . '&tags[]=' . $t->tag_id }}">#{{ $t->tag }}</a>, 
                    @endif
                @endforeach
            @endforeach
            </h4>
            <h1>{!! FA::icon('tags') !!}</h1>
        @endif
    </div>
    <div class="grey detail_box">&nbsp;</div>
    <div align="left" style="padding: 25px;">
        <button type="submit" class="btn btn-primary" onclick="window.history.back();">
            {!! FA::icon('hand-o-left') !!} @lang('messages.back-to-map')
        </button>
    </div>
@endsection
