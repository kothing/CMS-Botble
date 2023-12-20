<div class="row">
    <div class="col-md-4">
        <div class="white">
            <div class="br2 pa3 bg-light-blue mb3" style="box-shadow: 0 1px 1px #ccc;">
                <div class="media-body">
                    <div class="f3">
                        <span class="fw6">{{ $user->posts()->where('status', \Botble\Base\Enums\BaseStatusEnum::PUBLISHED)->count() }}</span>
                        <span class="fr"><i class="far fa-check-circle"></i></span>
                    </div>
                    <p>{{ trans('plugins/blog::member.published_posts') }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="white">
            <div class="br2 pa3 bg-light-red mb3" style="box-shadow: 0 1px 1px #ccc;">
                <div class="media-body">
                    <div class="f3">
                        <span class="fw6">{{ $user->posts()->where('status', \Botble\Base\Enums\BaseStatusEnum::PENDING)->count() }}</span>
                        <span class="fr"><i class="fas fa-user-clock"></i></span>
                    </div>
                    <p>{{ trans('plugins/blog::member.pending_posts') }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="white">
            <div class="br2 pa3 bg-light-silver mb3" style="box-shadow: 0 1px 1px #ccc;">
                <div class="media-body">
                    <div class="f3">
                        <span class="fw6">{{ $user->posts()->where('status', \Botble\Base\Enums\BaseStatusEnum::DRAFT)->count() }}</span>
                        <span class="fr"><i class="far fa-edit"></i></span>
                    </div>
                    <p>{{ trans('plugins/blog::member.draft_posts') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>