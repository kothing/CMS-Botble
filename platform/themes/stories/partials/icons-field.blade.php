@php
    Arr::set($attributes, 'class', Arr::get($attributes, 'class') . ' icon-select');
@endphp

{!! Form::customSelect($name, [$value => $value], $value, $attributes) !!}

@once
    @if (request()->ajax())
        <link media="all" type="text/css" rel="stylesheet" href="{{ Theme::asset()->url('css/vendor/elegant-icons.css') }}">
        <style>
            .icon-tiktok {
                background: url('{{ Theme::asset()->url('images/icon-tiktok.svg') }}');
                width: 10px;
                height: 10px;
                background-size: 100%;
            }
            .icon-discord {
                background: url('{{ Theme::asset()->url('images/icon-discord.svg') }}');
                width: 10px;
                height: 10px;
                background-size: 100%;
            }
        </style>
        <script src="{{ Theme::asset()->url('js/icons-field.js') }}?v=1.0.1"></script>
    @else
        @push('header')
            <link media="all" type="text/css" rel="stylesheet" href="{{ Theme::asset()->url('css/vendor/elegant-icons.css') }}">
            <style>
                .icon-tiktok {
                    background: url('{{ Theme::asset()->url('images/icon-tiktok.svg') }}');
                    width: 10px;
                    height: 10px;
                    background-size: 100%;
                }
                .icon-discord {
                    background: url('{{ Theme::asset()->url('images/icon-discord.svg') }}');
                    width: 10px;
                    height: 10px;
                    background-size: 100%;
                }
            </style>
            <script src="{{ Theme::asset()->url('js/icons-field.js') }}?v=1.0.1"></script>
        @endpush
    @endif
@endonce
