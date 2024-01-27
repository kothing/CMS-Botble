@php
    SeoHelper::setTitle(__('404 - Not found'));
    Theme::fireEventGlobalAssets();
@endphp

{!! Theme::partial('header') !!}

<main class="bg-grey pt-80 pb-50">
    <div class="container">
        <div class="row pt-80">
            <div class="col-lg-6 col-md-12 d-lg-block d-none pr-50"><img src="{{ Theme::asset()->url('images/page-not-found.png') }}" alt="{{ __('Not found') }}"></div>
            <div class="col-lg-6 col-md-12 pl-50 text-md-center text-lg-left">
                <h1 class="mb-30 font-weight-900 page-404">404</h1>
                <form action="{{ route('public.search') }}" method="get" class="search-form d-lg-flex open-search mb-30">
                    <i class="icon-search"></i>
                    <input class="form-control" name="q" type="text" placeholder="{{ __('Search...') }}">
                </form>
                <p>{{ __('The link you clicked may be broken or the page may have been removed.') }}<br> {{ __('visit the') }} <a href="{{ url('') }}">{{ __('Homepage') }}</a>
                </p>
                <div class="form-group">
                    <button type="submit" class="button button-contactForm mt-30"><a class="text-white" href="{{ url('') }}">{{ __('Homepage') }}</a></button>
                </div>
            </div>
        </div>
    </div>
</main>

{!! Theme::partial('footer') !!}


