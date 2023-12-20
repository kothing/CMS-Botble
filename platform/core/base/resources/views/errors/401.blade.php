@extends('core/base::errors.master')

@section('content')

    <div class="m-50">
        <div class="col-md-10">
            <h3>{{ trans('core/base::errors.401_title') }}</h3>
            <p>{{ trans('core/base::errors.reasons') }}</p>
            <ul>
                {!! BaseHelper::clean(trans('core/base::errors.401_msg')) !!}
            </ul>

            <p>{!! BaseHelper::clean(trans('core/base::errors.try_again', ['link' => route('dashboard.index')])) !!}</p>
        </div>
    </div>

@stop
