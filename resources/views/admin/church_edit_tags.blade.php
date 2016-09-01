@extends('layouts.app')

@section('content')
<div id="debug"></div>
<div class="container">
    <div class="row">
        <div align="center"><h3>{!! $msg or '' !!}</h3></div>
        <div class="col-md-10 col-md-offset-1">
            <h4>Edit Church Tags</h4>
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
            <form action="{{ URL::to('admin/church/edit/' . $church->id . '/tag/') }}" method="POST">
                {{ Form::token() }}
                <table>
                    <tr>
                        <td colspan="2"><h4>Address Record #</h4></td>
                    </tr><tr>
                        <td class="form_cell_label">&nbsp;</td><td></td>
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
