@extends('layouts.update')

@section('content')
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg ?? '' !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            <div align="center">
                <h3>{!! FA::icon('building-o') !!} Thank you for updating your church information with us!</h3>
            </div>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ URL::to('updatefromurl/church/edit/' . $church->id) }}" method="POST">
                {{ Form::token() }}
                <div class="panel panel-default tab_div">
                    <h4 align="center" class="mt-20">Choose language: @include('admin.language_radio')</h4>
                </div>
                <div class="panel panel-default tab_div">
                <table>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                    </tr>

    <!-- loop for multi-lingual name and description -->
                    @include('admin.church_name_and_desc')
    <!-- end loop -->
                    <tr>
                        <td></td><td>&nbsp;</td>
                    </tr>
    <!-- show all addresses, read-only -->
                    @foreach ($addresses as $cnt => $addr)
                        @include('admin.field_church_address')
                    @endforeach
                    <tr>
                        <td class="form_cell_label">Address Correct?</td>
                        <td class="text form_cell">
                            <label for="address_correct_yes">Yes</label>
                            <input type="radio" name="address_correct" id="address_correct_yes" value="1" checked/>
                            &nbsp;&nbsp;
                            <label for="address_correct_no">No</label>
                            <input type="radio" name="address_correct" id="address_correct_no" value="0" />
                        </td>
                    </tr>
    <!-- end loop -->
                    <tr>
                        <td>&nbsp;</td><td></td>
                    </tr>
                    <tr>
                        <td class="form_cell_label">Size</td><td class="text form_cell">
                            {{ Form::select('size_in_people', $sizes, $church->size_in_people, ['id' => 'size_in_people']) }}
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Languages Spoken</td><td class="text form_cell" style="">
                                @include('admin.language_picker')
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Url</td><td class="text form_cell">
                            <input type="text" id="url" name="url" value="{{ $church->url }}" placeholder="e.g. http://church.org"
                                @if ($errors->has('url'))
                                    class="alert-danger"
                                @endif
                            />
                        </td>
                    </tr><tr>
                        <td class="form_cell_label">Contact Phone</td><td class="text form_cell">
                            <input type="text" id="contact_phone" name="contact_phone" value="{{ $church->contact_phone }}"
                                @if ($errors->has('contact_phone'))
                                    class="alert-danger"
                                @endif
                            />
                        </td>
                    </tr><tr>
                        <td class="form_cell_label">Contact Email</td><td class="text form_cell">
                            <input type="text" id="contact_email" name="contact_email" value="{{ $church->contact_email }}"
                                @if ($errors->has('contact_email'))
                                    class="alert-danger"
                                @endif
                            />
                        </td>
                    </tr><tr>
                        <td class="form_cell_label">Organizations</td><td class="text form_cell">
                            {{ Form::select('church_organizations[]', $organizations, $church_organizations,
                                [
                                    'placeholder' => 'Select one or more',
                                    'id' => 'church_organizations',
                                    'multiple' => 'true',
                                    'size' => '4'
                                ]) }}
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td><td class="text form_cell">
                            <button type="submit" class="btn btn-primary">
                                {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Save
                            </button>
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td>
                        <td class="text form_cell"><a href="/update/church">キャンセル</a></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr>
                </table>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
