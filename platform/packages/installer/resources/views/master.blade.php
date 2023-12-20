<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('template_title', trans('packages/installer::installer.title'))</title>

    <link rel="icon" href="{{ asset('vendor/core/core/base/images/favicon.png') }}">
    <link href="{{ asset('vendor/core/core/base/libraries/font-awesome/css/fontawesome.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('vendor/core/packages/installer/css/style.css') }}?v={{ get_cms_version() }}" rel="stylesheet"/>

    <link rel="preconnect" href="{{ BaseHelper::getGoogleFontsURL() }}">
    <link href="{{ BaseHelper::getGoogleFontsURL() }}/css?family=Lato:400,700%7cPoppins:200,400,500,700" rel="stylesheet">

    @yield('styles')
</head>
<body>
    @php
        $currentStep = match (true) {
            Route::is('installers.welcome') => 1,
            Route::is('installers.requirements') => 2,
            Route::is('installers.environment') => 3,
            Route::is('installers.create_account') => 4,
            Route::is('installers.final') => 5,
            default => 1,
        };
    @endphp
    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 bg-opacity-25 min-h-screen h-auto justify-center items-center py-20">
        <div class="text-center mb-10">
            <h2 class="text-white font-semibold text-3xl">
                {{ trans('packages/installer::installer.installation') }}
            </h2>
        </div>
        <div class="bg-white w-full rounded-xl mx-auto max-w-7xl px-4 py-8 lg:py-8 lg:px-8 shadow-2xl">
            @include('packages/installer::partials.progress')
            <main class="pt-10 pb-4">
                @include('packages/installer::partials.alert')
                @yield('container')
            </main>
        </div>
    </div>

    @yield('scripts')
</body>
</html>
