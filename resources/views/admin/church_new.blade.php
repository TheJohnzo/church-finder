@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg or '' !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            <h4>Edit Church</h4>
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
            <form action="{{ URL::to('admin/church/new') }}" method="POST">
                {{ Form::token() }}
                <table>
                    <tr>
                        <td>&nbsp;</td><td></td>
                    </tr>

<!-- loop for multi-lingual name and description -->
                    @foreach ($languages as $lang)
                    <tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Name <span class="flag-icon flag-icon-{{ $lang['primary_country'] }}" style="background-size: contain;background-position: 50%;background-repeat: no-repeat;"></span></td><td class="text form_cell">
                            <input type="text" id="name_{{ $lang['code'] }}" name="name_{{ $lang['code'] }}" value="{{ old('description_' . $lang['code']) }}" 
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
                            >{{ old('description_' . $lang['code']) }}</textarea>
                        </td>
                    </tr>
                    @endforeach
<!-- end loop -->
                    <tr>
                        <td></td><td>&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Size</td><td class="text form_cell">
                            {{ Form::select('size_in_people', $sizes, old('size_in_people'), ['id' => 'size_in_people']) }}
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
                            <input type="text" id="url" name="url" value="{{ old('url') }}" placeholder="e.g. http://church.org" 
                                @if ($errors->has('url'))
                                    class="alert-danger"
                                @endif
                            />
                        </td>
                    </tr><tr>
                        <td class="form_cell_label">Contact Phone</td><td class="text form_cell">
                            <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" 
                                @if ($errors->has('contact_phone'))
                                    class="alert-danger"
                                @endif
                            />
                        </td>
                    </tr><tr>
                        <td class="form_cell_label">Contact Email</td><td class="text form_cell">
                            <input type="text" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" 
                                @if ($errors->has('contact_email'))
                                    class="alert-danger"
                                @endif
                            />
                        </td>
                    </tr><tr>
                        <td class="form_cell_label">Organizations</td><td class="text form_cell">
                            {{ Form::select('church_organizations[]', $organizations, old('church_organzations'), 
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
                        <td class="text form_cell"><a href="/admin/church">キャンセル</a></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr>
                </table>
            </form>

            </div>
        </div>
    </div>
</div>
@endsection
