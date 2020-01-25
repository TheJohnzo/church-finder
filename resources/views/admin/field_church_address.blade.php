@foreach ($languages as $lang)
<tr>
    <td class="form_cell_label">Address {{ ($addr->primary) ? '(PRIMARY)' : '' }}<span class="flag-icon flag-icon-{{ $lang['primary_country'] }}" style="background-size: contain;background-position: 50%;background-repeat: no-repeat;"></span></td>
    <td class="text form_cell"><input type="text" name="addr_{{ $lang->code }}" id="addr_{{ $lang->code }}_{{ $addr->id }}"
            value="{{ $address_labels[$addr->id][$lang->code] ?? '' }}" readonly="readonly" />
    </td>
</tr>
@endforeach
