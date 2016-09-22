<h5>{{ $info['name'] }}</h5>
<p style="width: 75%">{{ $info['description'] }}</p>
<p>{!! FA::icon('link') !!} <a href="/church/{{ $church_id }}">See more info</a></p>
<p style="width: 50%">
@foreach ($l->tag as $tag)
    @foreach ($tag->translation->all() as $t)
        @if ($t->language == $lang)
            <a href="{{ URL::to('search') . '?lang=' . $lang . '&tags[]=' . $t->tag_id }}">#{{ $t->tag }}</a>, 
        @endif
    @endforeach
@endforeach
</p>
