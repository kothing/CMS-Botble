{!! Theme::partial('header') !!}

<div class="container">
    <div class="row">
        <div class="col-lg-8">
            {!! Theme::content() !!}
        </div>
        <div class="col-lg-4 primary-sidebar sticky-sidebar">
            {!! display_ad('top-sidebar-ads', ['class' => 'mb-30']) !!}
            {!! dynamic_sidebar('primary_sidebar') !!}
            {!! display_ad('bottom-sidebar-ads', ['class' => 'mt-30 mb-30']) !!}
            <br>
            <br>
        </div>
    </div>
</div>

{!! Theme::partial('footer') !!}
