</div>
<footer class="page-footer bg-dark pt-50">
    <div class="container">
        <div class="row">
            @if (theme_option('address') || theme_option('website') || theme_option('contact_email') || theme_option('site_description'))
            <div class="col-lg-3 col-md-3 col-sm-6 col-12">
                <aside class="widget widget--transparent widget__footer widget__about">
                    <div class="widget__header">
                        <h3 class="widget__title">{{ __('About us') }}</h3>
                    </div>
                    <div class="widget__content">
                        <p>{{ theme_option('site_description') }}</p>
                        <div class="person-detail">
                            @if ($address = theme_option('address'))
                                <p><i class="ion-home"></i>{{ $address }}</p>
                            @endif
                            @if ($website = theme_option('website'))
                                <p><i class="ion-earth"></i><a href="{{ $website }}">{{ $website }}</a></p>
                            @endif
                            @if ($email = theme_option('contact_email'))
                                <p><i class="ion-email"></i><a href="mailto:{{ $email }}">{{ $email }}</a></p>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
            @endif
            {!! dynamic_sidebar('footer_sidebar') !!}
        </div>
    </div>
    <div class="page-footer__bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-sm-6 text-start">
                    <div class="page-copyright">
                        <p>{!! BaseHelper::clean(theme_option('copyright')) !!}</p>
                    </div>
                </div>
                @if (theme_option('social_links') && $socialLinks = json_decode(theme_option('social_links'), true))
                    <div class="col-md-4 col-sm-6 text-end">
                        <div class="page-footer__social">
                            <ul class="social social--simple">
                                @foreach($socialLinks as $socialLink)
                                    @if (count($socialLink) == 3 && $socialLink[2]['value'])
                                        <li>
                                            <a href="{{ $socialLink[2]['value'] }}"
                                               title="{{ $socialLink[0]['value'] }}" target="_blank">
                                                <i class="hi-icon {{ $socialLink[1]['value'] }}"></i>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</footer>
<div id="back2top"><i class="fa fa-angle-up"></i></div>

{!! Theme::footer() !!}

</body>
</html>
