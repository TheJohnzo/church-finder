@extends('layouts.update')

@section('content')
    <div class="container">
        <div class="row">
            <div align="center"><h3>{!! $msg or '' !!}</h3></div>
            <div class="col-md-10 col-md-offset-1">
                <div align="center">
                    <h3>{!! FA::icon('map') !!} Please enter a new address for your church </h3>
                </div>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            <li>{{ $errors->first('addr_ja') }}</li>
                        </ul>
                    </div>
                @endif
                @foreach ($addresses as $cnt => $addr)
                    @if ((isset($address_id) && $addr->id == $address_id) || !isset($address_id))
                        <div class="panel panel-default" style="background-color: #f2f2f2;" id="div_{{ $addr->id }}">
                            <form action="{{ URL::to('updatefromurl/address/' . $church->id) }}{{ isset($address_labels) ? '/save' : '' }}"
                                  method="POST">
                                {{ Form::token() }}
                                <input type="hidden" name="lang" value="{{ $lang }}" />
                                <table>

                                    @if (!isset($address_labels))
                                    <tr>
                                        <td colspan="2"><h4>{{ ($addr->id > 0) ? '' : 'Create New ' }}Address Record {{ ($addr->id > 0) ? '#' . ($cnt+1) : '' }}</h4></td>
                                    </tr><tr>
                                        <td class="form_cell_label">&nbsp;</td><td></td>
                                    </tr>
                                    <tr>
                                        <td class="form_cell_label">Address</td><td class="text form_cell"><input type="text" id="search_{{ $addr->id }}" name="search_{{ $addr->id }}" placeholder="123 Main St..."
                                           @if (count($errors->all()) > 0)
                                             class="alert-danger"
                                           @endif
                                        /></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td><td></td>
                                    </tr>
                                    <tr><td colspan="2">&nbsp;</td></tr>
                                    <tr>
                                        <td>&nbsp;</td><td class="text">
                                            <button type="submit" class="btn btn-primary">
                                                {!! FA::icon('hand-o-right') !!}&nbsp;&nbsp;Search
                                            </button>
                                        </td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td colspan="2">
                                            <h3>Is this correct?</h3><br />
                                            <h4>{{ $address_labels[$lang] }}</h4><br />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <!-- TODO need hidden address fields to pass data to next POST -->
                                            @foreach ($languages as $lang)
                                                <input type="text" name="addr_{{ $lang->code }}" id="addr_{{ $lang->code }}_{{ $addr->id }}"
                                                    value="{{ $address_labels[$lang->code] }}" />
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text" colspan="2" style="text-align: center;">
                                            <button type="submit" class="btn btn-primary" onclick="window.history.back()">
                                                {!! FA::icon('chevron-left') !!}&nbsp;&nbsp;Back
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                {!! FA::icon('check') !!}&nbsp;&nbsp;Save!
                                            </button>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td>&nbsp;</td><td></td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
