@extends('core/table::table')
@section('main-table')
    {!! Form::open(['url' => route('custom-fields.import'), 'class' => 'import-field-group']) !!}
        <input type="file" accept="application/json" class="hidden" id="import_json">
        @parent
    {!! Form::close() !!}
@stop