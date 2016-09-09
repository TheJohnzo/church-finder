@extends('layouts.map')

@section('content')
{{-- TODO need to fix multilingual support --}}
    <div align="left" style="padding: 25px;">
        <button type="submit" class="btn btn-primary" onclick="window.history.back();">
            {!! FA::icon('hand-o-left') !!} @lang('messages.back-to-map')
        </button>
    </div>
    <div align="center" style="width: 99%; padding-top: 15px; padding-bottom: 25px;">
        <h2>@foreach ($church->info()->get() as $info)
             @if ($info->language == $lang)
                {{ $info->name }}
             @endif
            @endforeach
        </h2>
        <h3>@foreach ($church->address()->first()->label as $label)
             @if ($label->language == $lang)
                {{ $label->addr }}
             @endif
            @endforeach
        </h3>
        <h4>@foreach ($church->info()->get() as $info)
             @if ($info->language == $lang)
                {{ $info->description }}
             @endif
            @endforeach
        </h4>

        @if ($church->contact_phone)
            <h4>{!! FA::icon('phone') !!} {{ $church->contact_phone }}</h4>
        @endif

        @if ($church->contact_email)
            <h4>{!! FA::icon('envelope') !!} {{ $church->contact_email }}</h4>
        @endif

        @if ($church->url)
            <h4>{!! FA::icon('link') !!} <a href="{{ $church->url }}" target="_blank">{{ $church->url }}</a></h4>
        @endif

        @if (count($church->meetingtime()->get()) > 0)
            <br />
            <h3>@lang('messages.service-times')</h3>
            @foreach ($church->meetingtime()->get() as $time)
                <h4>@lang('messages.day_' . $time->day_of_week) @ {{ $time->time }}
                    @foreach ($time->language()->get() as $language)
                        <span class="flag-icon flag-icon-{{ $languages[$language->language]->primary_country }}" 
                            style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></span>
                    @endforeach
                    {{-- TODO should check here to see if service address is different than above --}}
                </h4>
            @endforeach
        @endif

        @if (count($church->organization()->get()) > 0)
            <br />
            <h3>@lang('messages.related-orgs')</h3>
            @foreach ($church->organization()->get() as $org)
                <h4>{{ $org->name }}</h4>
                {{-- TODO need better layout and link to org details --}}
            @endforeach
        @endif

        @if (count($church->tag()->get()) > 0)
            <br />
            <h4>
            @foreach ($church->tag as $tag)
                @foreach ($tag->translation->all() as $t)
                    @if ($t->language == $lang)
                        #{{ $t->tag }}, 
                    @endif
                @endforeach
            @endforeach
            </h4>
            {{-- TODO tags should be links --}}
        @endif
    </div>
@endsection
