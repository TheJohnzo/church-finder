@extends('layouts.map')

@section('content')
{{-- TODO need to fix multilingual support --}}
    <div align="center" style="width: 99%; padding-top: 15px; padding-bottom: 25px;">
        
        <h2>{{ $church->info()->first()->name }}</h2>
        <h3>{{ $church->address()->first()->label->first()->addr }}</h3>
        <h4>{{ $church->info()->first()->description }}</h4>

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
            <h3>Service Times</h3>
            @foreach ($church->meetingtime()->get() as $time)
                <h4>{{ $days[$time->day_of_week] }} @ {{ $time->time }}
                    @foreach ($time->language()->get() as $lang)
                        <span class="flag-icon flag-icon-{{ $languages[$lang->language]->primary_country }}" 
                            style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></span>
                    @endforeach
                    {{-- TODO should check here to see if service address is different than above --}}
                </h4>
            @endforeach
        @endif

        @if (count($church->organization()->get()) > 0)
            <br />
            <h3>Related Orgs</h3>
            @foreach ($church->organization()->get() as $org)
                <h4>{{ $org->name }}</h4>
                {{-- TODO need better layout and link to org details --}}
            @endforeach
        @endif

        @if (count($church->tag()->get()) > 0)
            <br />
            <h3>Tags</h3>
            <h4>
            @foreach ($church->tag as $tag)
                {{ $tag->translation->first()->tag }}, 
            @endforeach
            </h4>
            {{-- TODO need to fix multilingual support, tags should be links --}}
        @endif
    </div>
@endsection
