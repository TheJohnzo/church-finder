@foreach ($languages as $lang)
    <label for="languages_{{ $lang->code }}" class="flag-icon flag-icon-{{ $lang['primary_country'] }}"
        style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></label>
    <input type="checkbox" name="languages[]" id="languages_{{ $lang->code }}" 
        {{ $selected_languages[$lang->code] ?? '' }}
    value="{{ $lang->code }}" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
@endforeach