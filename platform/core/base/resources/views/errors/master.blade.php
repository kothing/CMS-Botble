@extends('core/base::layouts.base')

@section ('page')
    <div class="page-wrapper">

        @include('core/base::layouts.partials.top-header')
        <div class="clearfix"></div>

        <div class="page-container page-container-gray">
            <div class="page-content" style="min-height: calc(100vh - 49px); height: 100%;">
                @yield('content')
            </div>
            <div class="clearfix"></div>
        </div>

        @include('core/base::layouts.partials.footer')

    </div>
@stop
