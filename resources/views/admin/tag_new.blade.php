@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg or '' !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            <h4>Tag Admin</h4>
            @if (count($errors) > 0)
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="panel panel-default tab_div">
            <form action="{{ URL::to('admin/tag/new') }}" method="POST">
                {{ Form::token() }}
                <table>
                    <tr>
                        <td colspan="2"><h4>Create New Tag</h4></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr>
                    @foreach ($languages as $lang)
                    <tr>
                        <td class="form_cell_label">Tag <span class="flag-icon flag-icon-{{ $lang['primary_country'] }}" 
                            style="background-size: contain;background-position: 50%;background-repeat: no-repeat;"></span></td>
                        <td class="text form_cell">{{ Form::text('tag_' . $lang->code, old('tag_' . $lang->code),
                            ['class' => ($errors->has('tag_' . $lang->code) ? 'alert-danger' : '')]
                            ) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td>&nbsp;</td><td class="text form_cell">
                            <button type="submit" class="btn btn-primary">
                                {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Save
                            </button>
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td>
                        <td class="text form_cell"><a href="/admin/tag">キャンセル</a></td>
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
