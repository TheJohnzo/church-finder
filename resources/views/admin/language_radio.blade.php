{{--
    Used to select which language you want to view the current page
    Typically only used for church people supply their own data updates
--}}
@foreach ($languages as $lang)
    <label for="radio_languages_{{ $lang->code }}" class="flag-icon flag-icon-{{ $lang['primary_country'] }}"
        style="background-size: contain;background-position: 50%;background-repeat: no-repeat;height:25px;width:40px;"></label>
    <input type="radio" name="languages[]" id="radio_languages_{{ $lang->code }}"
        {{ (request('lang') ?? 'ja' ) == $lang->code ? 'checked' : '' }}
    value="{{ $lang->code }}" onclick="radioChooseLanguage(this)" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
@endforeach
<script>
    function radioChooseLanguage(obj) {
        var url = window.location.href;
        url = url.substring(0, url.indexOf('?')) + '?lang=' + obj.value;;
        window.location = url;
    }
</script>