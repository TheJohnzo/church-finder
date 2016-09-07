@foreach ($languages as $lang)
    <span class="flag-icon flag-icon-{{ $lang['primary_country'] }}" 
        style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></span>
    <input type="checkbox" name="languages[]" id="languages_{{ $lang->code }}" 
        {{ $selected_languages[$lang->code] or '' }}
    value="{{ $lang->code }}" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
@endforeach