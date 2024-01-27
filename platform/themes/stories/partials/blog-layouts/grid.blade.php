@if ($posts->count() > 0)
    <div class="loop-list loop-list-style-1">
        <div class="row">
            @foreach ($posts as $post)
                <article class="col-md-{{ Theme::getLayoutName() == 'right-sidebar' ? 6 : 4 }} mb-40 wow fadeInUp  animated">
                    <div class="post-card-1 border-radius-10 hover-up">
                        {!! Theme::partial('components.post-card', compact('post')) !!}
                    </div>
                </article>
            @endforeach
        </div>
    </div>
    <div class="pagination-area mb-30 wow fadeInUp animated justify-content-start">
        {!! $posts->withQueryString()->onEachSide(1)->links() !!}
    </div>
@endif
