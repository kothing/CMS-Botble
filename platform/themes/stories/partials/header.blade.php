<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>

        {!! BaseHelper::googleFonts('https://fonts.googleapis.com/css2?family=' . urlencode(theme_option('primary_font', 'Noto Sans JP')) . ':wght@400;500;700;900&display=swap', false) !!}

        <style>
            :root {
                --color-primary: {{ theme_option('primary_color', '#5869DA') }};
                --color-secondary: {{ theme_option('secondary_color', '#2d3d8b') }};
                --color-danger: {{ theme_option('danger_color', '#e3363e') }};
                --primary-font: '{{ theme_option('primary_font', 'Noto Sans JP') }}', sans-serif;
            }
        </style>

        {!! Theme::header() !!}
    </head>
    <body @if (BaseHelper::siteLanguageDirection() == 'rtl') dir="rtl" @endif>
        {!! apply_filters(THEME_FRONT_BODY, null) !!}
        <div id="alert-container"></div>
        <div class="scroll-progress primary-bg"></div>
        @if (theme_option('preloader_enabled', 'no') == 'yes')
            <!-- Start Preloader -->
            <div class="preloader text-center">
                <div class="circle"></div>
            </div>
        @endif

        @if (is_plugin_active('blog'))
            <!--Offcanvas sidebar-->
            <aside id="sidebar-wrapper" class="custom-scrollbar offcanvas-sidebar" data-load-url="{{ route('theme.ajax-get-panel-inner') }}">
                <button class="off-canvas-close"><i class="elegant-icon icon_close"></i></button>
                <div class="sidebar-inner">
                    <div class="sidebar-inner-loading">
                        <div class="half-circle-spinner">
                            <div class="circle circle-1"></div>
                            <div class="circle circle-2"></div>
                        </div>
                    </div>
                </div>
            </aside>
        @endif
        <!-- Start Header -->
        <header class="main-header header-style-1 font-heading">
            @if (is_plugin_active('language'))
                <div class="header-select-language d-block d-sm-none">
                    <div class="container">
                        <div class="language-wrapper d-block d-sm-none">
                            <span>{{ __('Language') }}:</span> {!! $languages = Theme::partial('language-switcher') !!}
                        </div>
                    </div>
                </div>
            @endif
            <div class="header-top">
                <div class="container">
                    <div class="row pt-20 pb-20">
                        <div class="col-md-3 col-6">
                            @if (theme_option('logo'))
                                <a href="{{ route('public.single') }}"><img class="logo" src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}"></a>
                            @endif
                        </div>
                        <div class="col-md-9 col-6 text-right header-top-right">
                            {!! Menu::renderMenuLocation('header-menu', [
                                'view'    => 'top-menu',
                                'options' => ['class' => 'list-inline nav-topbar d-none d-md-inline'],
                            ]) !!}
                            @if (is_plugin_active('language'))
                                <div class="language-wrapper d-none d-md-inline">
                                    {!! $languages !!}
                                </div>
                                @if (trim($languages))
                                    <span class="vertical-divider mr-20 ml-20 d-none d-md-inline"></span>
                                @endif
                            @endif
                            <button class="search-icon d-inline"><span class="mr-15 text-muted font-small"><i class="elegant-icon icon_search mr-5"></i>{{ __('Search') }}</span></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-sticky">
                <div class="container align-self-center">
                    <div class="mobile_menu d-lg-none d-block"></div>
                    <div class="main-nav d-none d-lg-block float-left">
                        <nav>
                            {!! Menu::renderMenuLocation('main-menu', [
                                'view'    => 'menu',
                                'options' => ['class' => 'main-menu d-none d-lg-inline font-small'],
                            ]) !!}

                            {!! Menu::renderMenuLocation('main-menu', [
                                'view'    => 'menu',
                                'options' => ['class' => 'd-block d-lg-none text-muted', 'id' => 'mobile-menu', 'data-label' => __('Menu')],
                            ]) !!}
                        </nav>
                    </div>
                    <div class="float-right header-tools text-muted font-small">
                        <ul class="header-social-network d-inline-block list-inline mr-15">
                            @for ($i = 1; $i <= 5; $i++)
                                @if (theme_option('social_' . $i . '_url') && theme_option('social_' . $i . '_name'))
                                    <li class="list-inline-item"><a class="social-icon text-xs-center" style="background: {{ theme_option('social_' . $i . '_color') }}" href="{{ theme_option('social_' . $i . '_url') }}" target="_blank" title="{{ theme_option('social_' . $i . '_name') }}"><i class="elegant-icon {{ theme_option('social_' . $i . '_icon') }}"></i></a></li>
                                @endif
                            @endfor
                        </ul>
                        @if (is_plugin_active('blog'))
                            <div class="off-canvas-toggle-cover d-inline-block">
                                <div class="off-canvas-toggle hidden d-inline-block" id="off-canvas-toggle">
                                    <span></span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
        </header>
        <!--Start search form-->
        <div class="main-search-form">
            <div class="container">
                <div class="pt-50 pb-50 ">
                    <div class="row mb-20">
                        <div class="col-12 align-self-center main-search-form-cover m-auto">
                            <p class="text-center"><span class="search-text-bg">{{ __('Search') }}</span></p>
                            <form action="{{ is_plugin_active('blog') ? route('public.search') : '#' }}" class="search-header">
                                <div class="input-group w-100">
                                    <input type="text" name="q" class="form-control" placeholder="{{ __('Search stories, places and people') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-search bg-white" type="submit">
                                            <i class="elegant-icon icon_search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Start Main content -->
        <main>
