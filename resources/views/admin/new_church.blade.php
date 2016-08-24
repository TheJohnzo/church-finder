@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h4>Edit Church</h4>
            <div class="panel panel-default" style="background-color: #f2f2f2;">
                <form action="{{ URL::to('admin/church/new/') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <table>
                        <tr>
                            <td>Name</td><td class="text">
                                <input type="text" id="name" name="name" value="{{ old('name') }}" />
                            </td>
                        </tr><tr>
                            <td>Size</td><td class="text">
                                <select name="size_in_people" id="size_in_people">
                                    <option value="">select one...</option>
                                    @foreach ($sizes as $size)
                                    <option value="{{ $size->text }}"
                                        @if ($size->text === old('size_in_people'))
                                            selected="SELECTED"
                                        @endif
                                    >{{ $size->text }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr><tr>
                            <td>Url</td><td class="text">
                                <input type="text" id="url" name="url" value="{{ old('url') }}" placeholder="e.g. http://church.org" />
                            </td>
                        </tr><tr>
                            <td>Contact Phone</td><td class="text">
                                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" />
                            </td>
                        </tr><tr>
                            <td>Contact Email</td><td class="text">
                                <input type="text" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" />
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

