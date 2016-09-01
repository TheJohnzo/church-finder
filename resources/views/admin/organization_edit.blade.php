@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h4>Edit Organization</h4>
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
                <form action="{{ URL::to('admin/organization/edit/' . $organization->id) }}" method="POST">
                    {{ Form::token() }}
                    <table>
                        <tr>
                            <td class="form_cell_label">&nbsp;</td><td class="text form_cell"></td>
                        </tr><tr>
                            <td class="form_cell_label">ID</td><td class="text form_cell">{{ $organization->id }}</td>
                        </tr><tr>
                            <td class="form_cell_label">Name</td><td  class="text form_cell">
                                <input type="text" id="name" name="name" value="{{ $organization->name }}" />
                            </td>
                        </tr><tr>
                            <td class="form_cell_label">Size in Churches</td><td  class="text form_cell">
                                <input type="number" name="size_in_churches" id="size_in_churches" value="{{ $organization->size_in_churches }}" />
                            </td>
                        </tr><tr>
                            <td class="form_cell_label">National Url</td><td  class="text form_cell">
                                <input type="text" id="national_url" name="national_url" value="{{ $organization->national_url }}" placeholder="e.g. http://church.jp" />
                            </td>
                        </tr><tr>
                            <td class="form_cell_label">Global Url</td><td  class="text form_cell">
                                <input type="text" id="global_url" name="global_url" value="{{ $organization->global_url }}" placeholder="e.g. http://church.org" />
                            </td>
                        </tr><tr>
                            <td class="form_cell_label">Countries Active In</td><td  class="text form_cell">
                                {{ Form::select('countries[]', $countries, $active_countries, 
                                [
                                    'multiple' => true,
                                    'size' => 15,
                                ]) }}
                            </td>
                        </tr><tr>
                            <td>&nbsp;</td><td class="text">
                                <button type="submit" class="btn btn-primary">
                                    {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Save
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td class="text"><a href="Javascript:history.back();">キャンセル</a></td>
                        </tr><tr>
                            <td class="form_cell_label">&nbsp;</td><td class="text form_cell"></td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

