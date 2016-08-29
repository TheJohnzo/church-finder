@extends('layouts.app')

@section('content')
<script>
    function lookupAddress(id)
    {
        if ($('#search_' + id).val() !== '') {
            $.ajax({
                type: "POST",
                url: '/admin/church/lookupaddress',
                data: {addr: $('#search_' + id).val(), _token: '{{ csrf_token() }}'},
            }).done(function( data ) {
                $('#debug').html(data);
                $.each(jQuery.parseJSON(data), function( index, value ) {
                    $('#addr_' + index + '_' + id).val(value);
                    $('#addr_' + index + '_' + id).delay(100).fadeOut().fadeIn('slow');
                });
            }).fail(function(xhr, status, error) {
                $('#debug').html(xhr.responseText);
            });
        }
    }
</script>
<div id="debug"></div>
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg or '' !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            <h4>Edit Church Address</h4>
            @include('admin.church_menu')
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="panel panel-default" style="background-color: #f2f2f2;">
            @foreach ($addresses as $cnt => $addr)
            <form action="{{ URL::to('admin/church/edit/' . $church->id . '/address/' . $addr->id) }}" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <table>
                    <tr>
                        <td colspan="2"><h4>Address Record #{{ $cnt+1 }}</h4></td>
                    </tr><tr>
                        <td class="form_cell_label">&nbsp;</td><td></td>
                    </tr><tr>
                        <td>Search</td><td class="text form_cell"><input type="text" id="search_{{ $addr->id }}" placeholder="123 Main St..." style="width: 90%" /></td>
                    </tr><tr>
                        <td>&nbsp;</td><td class="text">{!! FA::icon('hand-o-right') !!}<a href"#" onclick="lookupAddress({{ $addr->id }})">Lookup Addresses</a></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr>
                    @include('admin.edit_church_address_fields')
                    <tr>
                        <td>&nbsp;</td><td class="text">
                            <button type="submit" class="btn btn-primary">
                                {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Save
                            </button>
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td>
                        <td class="text form_cell"><a href="/admin/church">キャンセル</a></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr>
                </table>
            </form>
            @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
