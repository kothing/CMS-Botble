@if ($sidebar == 'footer_sidebar')
    <div class="col-lg-3 col-md-3 col-sm-6 col-12">
        <aside class="widget widget--transparent widget__footer">
            @else
                <aside class="widget widget--transparent">
                    @endif
                    <div class="widget__header">
                        <h3 class="widget__title">{{ $config['name'] }}</h3>
                    </div>
                    <div class="widget__content">
                        {!!
                            Menu::generateMenu([
                                'slug'    => $config['menu_id'],
                                'options' => ['class' => ($config['menu_id'] == 'social' ? 'social social--simple social--widget' : 'list list--fadeIn') . ($sidebar == 'footer_sidebar' ? ' list--light' : '') ]
                            ])
                        !!}
                    </div>
                </aside>
        @if ($sidebar == 'footer_sidebar')
    </div>
@endif
