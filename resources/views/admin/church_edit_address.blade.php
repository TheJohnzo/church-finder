@extends('layouts.app')

@section('content')
<script>
    function lookupAddress(id)
    {
        if ($('#search_' + id).val() !== '') {
            $('#search_' + id).removeClass('alert-danger');
            $.ajax({
                type: "POST",
                url: '/admin/church/lookupaddress',
                data: {addr: $('#search_' + id).val(), _token: '{{ csrf_token() }}'},
            }).done(function( data ) {
                $('#debug').html(data);
                if (data == 'NOT_FOUND') {
                    $('#search_' + id).addClass('alert-danger');
                    alert('No address found, please try again.')
                }
                $.each(jQuery.parseJSON(data), function( index, value ) {
                    $('#addr_' + index + '_' + id).val(value);
                    $('#addr_' + index + '_' + id).delay(100).fadeOut().fadeIn('slow');
                });
            }).fail(function(xhr, status, error) {
                $('#debug').html(xhr.responseText);
            });
        }
    }
    function deleteAddress(id) {
        $('#div_' + id).addClass('alert-danger');
        if (confirm('Are you sure you want to delete this address record?')) {
            window.location = '/admin/church/{{ $church->id }}/address/delete/' + id;
        }
        $('#div_' + id).removeClass('alert-danger');
    }
</script>
<div id="debug"></div>
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg or '' !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            @include('admin.church_menu')
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        <li>{{ $errors->first('addr_ja') }}</li>
                    </ul>
                </div>
            @endif
            @foreach ($addresses as $cnt => $addr)
            @if ((isset($address_id) && $addr->id == $address_id) || !isset($address_id))
            <div class="panel panel-default" style="background-color: #f2f2f2;" id="div_{{ $addr->id }}">
            <form action="{{ URL::to('admin/church/' . $church->id . '/address/') }}/{{ ($addr->id > 0) ? $addr->id : 'new' }}" method="POST">
                {{ Form::token() }}
                <table>
                    <tr>
                        <td colspan="2"><h4>{{ ($addr->id > 0) ? '' : 'Create New ' }}Address Record {{ ($addr->id > 0) ? '#' . ($cnt+1) : '' }}</h4></td>
                    </tr><tr>
                        <td class="form_cell_label">&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Search</td><td class="text form_cell"><input type="text" id="search_{{ $addr->id }}" placeholder="123 Main St..." 
                            @if (count($errors->all()) > 0)
                                class="alert-danger"
                            @endif
                            /></td>
                    </tr><tr>
                        <td>&nbsp;</td><td class="text">{!! FA::icon('hand-o-right') !!}<a href"#" onclick="lookupAddress({{ $addr->id }})">Lookup Addresses</a></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr>
                    @include('admin.field_church_address')
                    <tr>
                        <td class="form_cell_label">Primary</td>
                        <td class="form_cell text">{{ Form::checkbox('primary', '1', $addr->primary)}}</td>
                    </tr><tr>
                        <td>&nbsp;</td><td class="text">
                            <button type="submit" class="btn btn-primary">
                                {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Save
                            </button> 
                            @if ($addr->id > 0) 
                                or <a href="#" onclick="deleteAddress({{ $addr->id }})">Delete</a>
                            @endif
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td>
                        <td class="text form_cell"><a href="/admin/church">キャンセル</a></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr>
                </table>
            </form>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endsection
