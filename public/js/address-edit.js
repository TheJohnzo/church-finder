function lookupAddress(id)
{
    if ($('#search_' + id).val() !== '') {
        $('#search_' + id).removeClass('alert-danger');
        $.ajax({
            type: "POST",
            url: '/admin/church/lookupaddress',
            data: {addr: $('#search_' + id).val(), _token: $("input[name=_token]").val()},
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