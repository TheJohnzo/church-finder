@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h4>Edit Organization</h4>
            <div class="panel panel-default" style="background-color: #f2f2f2;">
            <form action="{{ URL::to('admin/organization/new/') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <table>
                        <tr>
                            <td>Name</td><td class="text">
                                <input type="text" id="name" name="name" value="{{ old('name') }}" />
                            </td>
                        </tr><tr>
                            <td>Size</td><td class="text">
                                <input type="number" name="size_in_churches" id="size_in_churches" value="{{ old('size_in_churches') }}" />
                            </td>
                        </tr><tr>
                            <td>National Url</td><td class="text">
                                <input type="text" id="national_url" name="national_url" value="{{ old('url') }}" placeholder="e.g. http://church.jp" />
                            </td>
                        </tr><tr>
                            <td>Global Url</td><td class="text">
                                <input type="text" id="global_url" name="global_url" value="{{ old('global_url') }}" placeholder="e.g. http://church.org" />
                            </td>
                        </tr><tr>
                            <td>&nbsp;</td><td class="text">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-primary">
                                    {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Save
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td class="text"><a href="Javascript:history.back();">キャンセル</a></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

