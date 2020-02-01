@foreach ($languages as $lang)
    @if (!isset($chosen_lang) || (isset($chosen_lang) && $lang->code == $chosen_lang))
        <tr>
            <td>&nbsp;</td><td></td>
        </tr><tr>
            <td class="form_cell_label">Name
                @if (!isset($chosen_lang))
                    <span class="flag-icon flag-icon-{{ $lang['primary_country'] }}" style="background-size: contain;background-position: 50%;background-repeat: no-repeat;"></span>
                @endif
            </td>
            <td class="text form_cell">
                <input type="text" id="name_{{ $lang['code'] }}" name="name_{{ $lang['code'] }}" value="{{ @$infos[$lang['code']]['name'] }}"
                    @if ($errors->has('name_' . $lang['code']))
                        class="alert-danger"
                    @endif
                />
            </td>
        </tr><tr>
        </tr><tr>
            <td class="form_cell_label">Description</td><td class="text form_cell">
                <textarea id="description_{{ $lang['code'] }}" name="description_{{ $lang['code'] }}"
                    @if ($errors->has('description_' . $lang['code']))
                        class="alert-danger"
                    @endif
                >{{ @$infos[$lang['code']]['description'] }}</textarea>
            </td>
        </tr>
    @endif
@endforeach