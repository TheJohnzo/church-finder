<a href="/{{ Request::path() . '?sort=' . $field . '&dir=asc&search=' . $search }}" 
    class="sort_link{{ $sort == $field && $dir == 'asc' ? '_active' : '' }}">{!! FA::icon('arrow-circle-up') !!}</a>
<a href="/{{ Request::path() . '?sort=' . $field . '&dir=desc&search=' . $search }}" 
    class="sort_link{{ $sort == $field && $dir == 'desc' ? '_active' : '' }}">{!! FA::icon('arrow-circle-down') !!}</a>