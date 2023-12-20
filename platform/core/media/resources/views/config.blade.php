<script>
    "use strict";
    var RV_MEDIA_URL = {!! json_encode(RvMedia::getUrls()) !!};
    var RV_MEDIA_CONFIG = {!! json_encode([
        'permissions'  => RvMedia::getPermissions(),
        'translations' => trans('core/media::media.javascript'),
        'pagination'   => [
            'paged'                => RvMedia::getConfig('pagination.paged'),
            'posts_per_page'       => RvMedia::getConfig('pagination.per_page'),
            'in_process_get_media' => false,
            'has_more'             => true,
        ],
        'chunk'         => RvMedia::getConfig('chunk'),
        'random_hash'   => setting('media_random_hash') ?: null,
        'default_image' => RvMedia::getDefaultImage(),
    ]) !!}
</script>
