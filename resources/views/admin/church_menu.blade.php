<h4>#{{ $church->id}} - {{ $church->info()->first()->name }}</h4>
<ul class="nav nav-tabs">
    <li class="{{ $church_page ?? '' }}"><a href="/admin/church/edit/{{ $church->id }}">Church Details</a></li>
    <li class="{{ $address_page ?? '' }}"><a href="/admin/church/{{ $church->id }}/address">Addresses</a></li>
    <li class="{{ $meeting_page ?? '' }}"><a href="/admin/church/{{ $church->id }}/meetingtime">Meeting Times</a></li>
    <li class="{{ $tag_page ?? '' }}"><a href="/admin/church/{{ $church->id }}/tag">Tags</a></li>
</ul>