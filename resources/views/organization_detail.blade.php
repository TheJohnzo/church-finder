@extends('layouts.map')

@section('content')
    <div align="left" style="padding: 25px;">
        <button type="submit" class="btn btn-primary" onclick="window.history.back();">
            {!! FA::icon('hand-o-left') !!} @lang('messages.back-to-map')
        </button>
    </div>
    <div align="center" style="width: 99%; padding-top: 15px; padding-bottom: 25px;">
        <h2>{{ $org->info()->where('language', $lang)->first()->name }}</h2>
        {{-- TODO logo --}}
        <div align="center">
        <h4 class="centered">@foreach ($org->info()->get() as $info)
             @if ($info->language == $lang)
                {{ $info->description }}
             @endif
            @endforeach
        </h4>
        </div>
        @if ($org->national_url)
            <h4><span class="flag-icon flag-icon-jp" 
                    style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></span>
                {!! FA::icon('link') !!} <a href="{{ $org->national_url }}" target="_blank">{{ $org->national_url }}</a></h4>
        @endif

        @if ($org->global_url)
            <h4><span class="flag-icon flag-icon-un" 
                    style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></span>
                {!! FA::icon('link') !!} <a href="{{ $org->global_url }}" target="_blank">{{ $org->global_url }}</a></h4>
        @endif

        @if ($org->size_in_churches)
            <h3>Number of Churches</h3>
            <h4>{!! FA::icon('users') !!} {{ $org->size_in_churches }}</h4>
        @endif

        @if (count($org->countries()->get()) > 0)
            <br />
            <h3>@lang('messages.countries-in')</h3>
            <div style="width: 50%;" align="center">
            @foreach ($org->countries()->get() as $c)
                <span class="flag-icon flag-icon-{{ strtolower($c->code) }}" 
                    style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></span>
            @endforeach
            </div>
        @endif

    </div>
@endsection
