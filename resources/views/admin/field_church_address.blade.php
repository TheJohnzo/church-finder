@foreach ($languages as $lang)
    {{-- If there is a chosen_lang, only show that one --}}
    @if (!isset($chosen_lang) || (isset($chosen_lang) && $lang->code == $chosen_lang))
        <tr>
            <td class="form_cell_label">Address {{ ($addr->primary) ? '(PRIMARY)' : '' }}
                @if (!isset($chosen_lang))
                    <span class="flag-icon flag-icon-{{ $lang['primary_country'] }}" style="background-size: contain;background-position: 50%;background-repeat: no-repeat;"></span>
                @endif
            </td>
            <td class="text form_cell"><input type="text" name="addr_{{ $lang->code }}" id="addr_{{ $lang->code }}_{{ $addr->id }}"
                    value="{{ $address_labels[$addr->id][$lang->code] ?? '' }}" readonly="readonly" />
            </td>
        </tr>
    @endif
@endforeach
