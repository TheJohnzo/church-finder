@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg or '' !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            <h4>Church Meeting Times</h4>
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
            <div class="panel panel-default tab_div">
            <form action="{{ URL::to('admin/church/' . $church->id . '/meetingtime/edit/' . $time['id']) }}" method="POST">
                {{ Form::token() }}
                <table>
                    <tr>
                        <td colspan="2"><h4>Edit Meeting Time</h4></td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Day of Week</td>
                        <td class="text form_cell">
                        {{ Form::select('day_of_week', $days, $time['day_of_week']) }}</td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Time</td>
                        <td class="text form_cell">{{ Form::text('time', $time['time'], [
                            'class' => ($errors->has('time')) ? 'alert-danger' : '',
                            'placeholder' => 'E.g. 10:30 or 14:00'
                            ]) }}</td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Service Languages</td>
                        <td class="text form_cell {{ ($errors->has('languages')) ? 'alert-danger' : '' }}">
                        @include('admin.language_picker')
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td><td></td>
                    </tr><tr>
                        <td class="form_cell_label">Address</td>
                        <td class="text form_cell">
                        {{ Form::select('address', $addresses, $time['church_address_id']) }}
                        </td>
                    </tr><tr>
                        <td class="form_cell_label">&nbsp;</td>
                        <td class="text form_cell"></td>
                    </tr><tr>
                        <td>&nbsp;</td><td class="text form_cell">
                            <button type="submit" class="btn btn-primary">
                                {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Save
                            </button>
                        </td>
                    </tr><tr>
                        <td>&nbsp;</td>
                        <td class="text form_cell"><a href="/admin/church/{{ $church->id }}/meetingtime">キャンセル</a></td>
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
