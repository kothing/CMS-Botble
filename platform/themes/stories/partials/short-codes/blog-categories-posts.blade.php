<div class="bg-grey pt-50 pb-50">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                @if (!empty($category))
                    <div class="post-module-2">
                        <div class="widget-header-1 position-relative mb-30  wow fadeInUp animated">
                            <h5 class="mt-5 mb-30">{{ $category->name }}</h5>
                        </div>
                        <div class="loop-list loop-list-style-1">
                            <div class="row">
                                @foreach($category->posts->sortByDesc('id')->take(4) as $post)
                                    <article class="col-md-6 mb-40 wow fadeInUp  animated">
                                        <div class="post-card-1 border-radius-10 hover-up">
                                            {!! Theme::partial('components.post-card', compact('post')) !!}
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @php($latestPosts = get_latest_posts(3, !empty($category) ? $category->posts->sortByDesc('id')->take(4)->pluck('id')->all() : [], ['slugable', 'categories', 'categories.slugable']))
                @if ($latestPosts->count())
                    <div class="post-module-3">
                        <div class="widget-header-1 position-relative mb-30">
                            <h5 class="mt-5 mb-30">{{ __('Latest posts') }}</h5>
                        </div>
                        <div class="loop-list loop-list-style-1">
                            @foreach($latestPosts as $post)
                                <article class="hover-up-2 transition-normal wow fadeInUp animated">
                                    {!! Theme::partial('components.post-list-item', compact('post')) !!}
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            <div class="col-lg-4">
                <div class="widget-area">
                    {!! display_ad('top-sidebar-ads', ['class' => 'mb-30']) !!}
                    {!! dynamic_sidebar('primary_sidebar') !!}
                    {!! display_ad('bottom-sidebar-ads', ['class' => 'mt-30 mb-30']) !!}
                </div>
            </div>
        </div>
    </div>
</div>
