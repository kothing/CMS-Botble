<section data-background="{{ Theme::get('breadcrumbBannerImage') ?: (theme_option('default_breadcrumb_banner_image') ? RvMedia::getImageUrl(theme_option('default_breadcrumb_banner_image')) : Theme::asset()->url('images/page-intro-02.jpg')) }}" class="section page-intro pt-50 pb-50 bg-cover">
    <div style="opacity: 0.7" class="bg-overlay"></div>
    <div class="container">
        <h3 class="page-intro__title">{{ Theme::get('section-name') ?: SeoHelper::getTitle() }}</h3>
        {!! Theme::breadcrumb()->render() !!}
    </div>
</section>
