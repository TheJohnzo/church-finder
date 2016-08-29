<ul class="nav nav-tabs">
    <li class="{{ $church_page or '' }}"><a href="/admin/church/edit/{{ $church->id }}">Church Details</a></li>
    <li class="{{ $address_page or '' }}"><a href="/admin/church/edit/{{ $church->id }}/address">Addresses</a></li>
    <li class="{{ $meeting_page or '' }}"><a href="/admin/church/edit/{{ $church->id }}/meetingtimes">Meeting Times</a></li>
    <li class="{{ $tag_page or '' }}"><a href="/admin/church/edit/{{ $church->id }}/tag">Tags</a></li>
</ul>