<div class="rv-media-container">
    <div class="rv-media-wrapper">
        <input type="checkbox" id="media_aside_collapse" class="fake-click-event hidden">
        <input type="checkbox" id="media_details_collapse" class="fake-click-event hidden">
        <aside class="rv-media-aside @if (RvMedia::getConfig('sidebar_display') != 'vertical') rv-media-aside-hide-desktop @endif">
            <label for="media_aside_collapse" class="collapse-sidebar">
                <i class="far fa-window-close"></i>
            </label>
            <div class="rv-media-block rv-media-filters">
                <div class="rv-media-block-title">
                    {{ trans('core/media::media.filter') }}
                </div>
                <div class="rv-media-block-content">
                    <ul class="rv-media-aside-list">
                        <li>
                            <a href="#" class="js-rv-media-change-filter" data-type="filter" data-value="everything">
                                <i class="fa fa-recycle"></i> {{ trans('core/media::media.everything') }}
                            </a>
                        </li>
                        @if (array_key_exists('image', RvMedia::getConfig('mime_types', [])))
                            <li>
                                <a href="#" class="js-rv-media-change-filter" data-type="filter" data-value="image">
                                    <i class="fa fa-file-image"></i> {{ trans('core/media::media.image') }}
                                </a>
                            </li>
                        @endif
                        @if (array_key_exists('video', RvMedia::getConfig('mime_types', [])))
                            <li>
                                <a href="#" class="js-rv-media-change-filter" data-type="filter" data-value="video">
                                    <i class="fa fa-file-video"></i> {{ trans('core/media::media.video') }}
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="#" class="js-rv-media-change-filter" data-type="filter" data-value="document">
                                <i class="fa fa-file"></i> {{ trans('core/media::media.document') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="rv-media-block rv-media-view-in">
                <div class="rv-media-block-title">
                    {{ trans('core/media::media.view_in') }}
                </div>
                <div class="rv-media-block-content">
                    <ul class="rv-media-aside-list">
                        <li>
                            <a href="#" class="js-rv-media-change-filter" data-type="view_in" data-value="all_media">
                                <i class="fa fa-globe"></i> {{ trans('core/media::media.all_media') }}
                            </a>
                        </li>
                        @if (RvMedia::hasAnyPermission(['folders.destroy', 'files.destroy']))
                            <li>
                                <a href="#" class="js-rv-media-change-filter" data-type="view_in" data-value="trash">
                                    <i class="fa fa-trash"></i> {{ trans('core/media::media.trash') }}
                                </a>
                            </li>
                        @endif
                        <li>
                            <a href="#" class="js-rv-media-change-filter" data-type="view_in" data-value="recent">
                                <i class="fa fa-clock"></i> {{ trans('core/media::media.recent') }}
                            </a>
                        </li>
                        <li>
                            <a href="#" class="js-rv-media-change-filter" data-type="view_in" data-value="favorites">
                                <i class="fa fa-star"></i> {{ trans('core/media::media.favorites') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
        <div class="rv-media-main-wrapper">
            <header class="rv-media-header">
                <div class="rv-media-top-header">
                    <div class="rv-media-actions">
                        <label for="media_aside_collapse" class="btn btn-danger collapse-sidebar">
                            <i class="fa fa-bars"></i>
                        </label>
                        @if (RvMedia::hasPermission('files.create'))
                            <button class="btn btn-success js-dropzone-upload">
                                <i class="fas fa-cloud-upload-alt"></i> {{ trans('core/media::media.upload') }}
                            </button>

                            <button class="btn btn-success js-download-action" type="button">
                                <i class="fas fa-cloud-download-alt"></i> {{ trans('core/media::media.download_link') }}
                            </button>
                        @endif
                        @if (RvMedia::hasPermission('folders.create'))
                            <button class="btn btn-success js-create-folder-action" type="button">
                                <i class="fa fa-folder"></i> {{ trans('core/media::media.create_folder') }}
                            </button>
                        @endif
                        <button class="btn btn-success js-change-action" data-type="refresh">
                            <i class="fas fa-sync"></i> {{ trans('core/media::media.refresh') }}
                        </button>

                        @if (RvMedia::getConfig('sidebar_display') != 'vertical')
                            <div class="btn-group" role="group">
                                <div class="dropdown">
                                    <button class="btn btn-success dropdown-toggle js-rv-media-change-filter-group js-filter-by-type" type="button" data-bs-toggle="dropdown">
                                        <i class="fa fa-filter"></i> {{ trans('core/media::media.filter') }} <span class="js-rv-media-filter-current"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="#" class="js-rv-media-change-filter" data-type="filter" data-value="everything">
                                                <i class="fa fa-recycle"></i> {{ trans('core/media::media.everything') }}
                                            </a>
                                        </li>
                                        @if (array_key_exists('image', RvMedia::getConfig('mime_types', [])))
                                            <li>
                                                <a href="#" class="js-rv-media-change-filter" data-type="filter" data-value="image">
                                                    <i class="fa fa-file-image"></i> {{ trans('core/media::media.image') }}
                                                </a>
                                            </li>
                                        @endif
                                        @if (array_key_exists('video', RvMedia::getConfig('mime_types', [])))
                                            <li>
                                                <a href="#" class="js-rv-media-change-filter" data-type="filter" data-value="video">
                                                    <i class="fa fa-file-video"></i> {{ trans('core/media::media.video') }}
                                                </a>
                                            </li>
                                        @endif
                                        <li>
                                            <a href="#" class="js-rv-media-change-filter" data-type="filter" data-value="document">
                                                <i class="fa fa-file"></i> {{ trans('core/media::media.document') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <div class="btn-group" role="group">
                                <div class="dropdown">
                                    <button class="btn btn-success dropdown-toggle js-rv-media-change-filter-group js-filter-by-view-in" type="button" data-bs-toggle="dropdown">
                                        <i class="fa fa-eye"></i> {{ trans('core/media::media.view_in') }} <span class="js-rv-media-filter-current"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a href="#" class="js-rv-media-change-filter" data-type="view_in" data-value="all_media">
                                                <i class="fa fa-globe"></i> {{ trans('core/media::media.all_media') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="js-rv-media-change-filter" data-type="view_in" data-value="trash">
                                                <i class="fa fa-trash"></i> {{ trans('core/media::media.trash') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="js-rv-media-change-filter" data-type="view_in" data-value="recent">
                                                <i class="fa fa-clock"></i> {{ trans('core/media::media.recent') }}
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" class="js-rv-media-change-filter" data-type="view_in" data-value="favorites">
                                                <i class="fa fa-star"></i> {{ trans('core/media::media.favorites') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @endif

                        @if (RvMedia::hasAnyPermission(['folders.destroy', 'files.destroy']))
                            <button class="btn btn-danger js-files-action hidden" data-action="empty_trash">
                                <i class="fa fa-trash"></i> {{ trans('core/media::media.empty_trash') }}
                            </button>
                        @endif

                    </div>
                    <div class="rv-media-search">
                        <form class="input-search-wrapper" action="" method="GET">
                            <input type="text" class="form-control" placeholder="{{ trans('core/media::media.search_file_and_folder') }}">
                            <button class="btn btn-link" type="submit">
                                <i class="fa fa-search"></i>
                            </button>
                        </form>
                    </div>
                </div>
                <div class="rv-media-bottom-header">
                    <div class="rv-media-breadcrumb">
                        <ul class="breadcrumb"></ul>
                    </div>
                    <div class="rv-media-tools">
                        <div class="btn-group" role="group">
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                    {{ trans('core/media::media.sort') }} <i class="fa fa-sort-alpha-down"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="#"
                                           class="js-rv-media-change-filter"
                                           data-type="sort_by"
                                           data-value="name-asc">
                                            <i class="fas fa-sort-alpha-up"></i> {{ trans('core/media::media.file_name_asc') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#"
                                           class="js-rv-media-change-filter"
                                           data-type="sort_by"
                                           data-value="name-desc">
                                            <i class="fas fa-sort-alpha-down"></i> {{ trans('core/media::media.file_name_desc') }}
                                        </a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="#"
                                           class="js-rv-media-change-filter"
                                           data-type="sort_by"
                                           data-value="created_at-asc">
                                            <i class="fas fa-sort-numeric-up"></i> {{ trans('core/media::media.uploaded_date_asc') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#"
                                           class="js-rv-media-change-filter"
                                           data-type="sort_by"
                                           data-value="created_at-desc">
                                            <i class="fas fa-sort-numeric-down"></i> {{ trans('core/media::media.uploaded_date_desc') }}
                                        </a>
                                    </li>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="#"
                                           class="js-rv-media-change-filter"
                                           data-type="sort_by"
                                           data-value="size-asc">
                                            <i class="fas fa-sort-numeric-up"></i> {{ trans('core/media::media.size_asc') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#"
                                           class="js-rv-media-change-filter"
                                           data-type="sort_by"
                                           data-value="size-desc">
                                            <i class="fas fa-sort-numeric-down"></i> {{ trans('core/media::media.size_desc') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="dropdown rv-dropdown-actions disabled mx-2">
                                <button class="btn btn-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown">
                                    {{ trans('core/media::media.actions') }} &nbsp;<i class="fa fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu"></ul>
                            </div>
                        </div>
                        <div class="btn-group js-rv-media-change-view-type mx-0" role="group">
                            <button class="btn btn-secondary" type="button" data-type="tiles">
                                <i class="fa fa-th-large"></i>
                            </button>
                            <button class="btn btn-secondary" type="button" data-type="list">
                                <i class="fa fa-th-list"></i>
                            </button>
                        </div>
                        <label for="media_details_collapse" class="btn btn-link collapse-panel">
                            <i class="fas fa-sign-out-alt"></i>
                        </label>
                    </div>
                </div>
            </header>

            <main class="rv-media-main">
                <div class="rv-media-items"></div>
                <div class="rv-media-details hidden">
                    <div class="rv-media-thumbnail">
                        <i class="far fa-image"></i>
                    </div>
                    <div class="rv-media-description">
                        <div class="rv-media-name">
                            <p>{{ trans('core/media::media.nothing_is_selected') }}</p>
                        </div>
                    </div>
                </div>
            </main>
            <footer class="rv-media-footer hidden">
                <button type="button" class="btn btn-danger btn-lg js-insert-to-editor">{{ trans('core/media::media.insert') }}</button>
            </footer>
        </div>
        <div class="rv-upload-progress hide-the-pane">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ trans('core/media::media.upload_progress') }}</h3>
                    <a href="javascript:void(0);" class="close-pane">
                        <i class="fa fa-times"></i>
                    </a>
                </div>
                <div class="panel-body">
                    <ul class="rv-upload-progress-table"></ul>
                </div>
            </div>
        </div>
    </div>

    <div class="rv-modals">
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_add_folder">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">
                            <i class="fa fa-folder"></i> {{ trans('core/media::media.create_folder') }}
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">

                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="rv-form form-add-folder">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="{{ trans('core/media::media.folder_name') }}">
                                <button class="btn btn-success rv-btn-add-folder" type="submit">{{ trans('core/media::media.create') }}</button>
                            </div>
                        </form>
                        <div class="modal-notice"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_rename_items">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form class="rv-form form-rename">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fab fa-windows"></i> {{ trans('core/media::media.rename') }}
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">

                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="rename-items"></div>
                            <div class="modal-notice"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/media::media.close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('core/media::media.save_changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_alt_text_items">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form class="rv-form form-alt-text">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fas fa-file-signature"></i> {{ trans('core/media::media.alt_text') }}
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alt-text-items"></div>
                            <div class="modal-notice"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/media::media.close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('core/media::media.save_changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_trash_items">
            <div class="modal-dialog modal-danger" role="document">
                <div class="modal-content">
                    <form class="rv-form form-delete-items">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fab fa-windows"></i> {{ trans('core/media::media.move_to_trash') }}
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">

                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{ trans('core/media::media.confirm_trash') }}</p>
                            <div class="modal-notice"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">{{ trans('core/media::media.confirm') }}</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ trans('core/media::media.close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_delete_items">
            <div class="modal-dialog modal-danger" role="document">
                <div class="modal-content">
                    <form class="rv-form form-delete-items">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fab fa-windows"></i> {{ trans('core/media::media.confirm_delete') }}
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">

                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{ trans('core/media::media.confirm_delete_description') }}</p>
                            <div class="modal-notice"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">{{ trans('core/media::media.confirm') }}</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ trans('core/media::media.close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_empty_trash">
            <div class="modal-dialog modal-danger" role="document">
                <div class="modal-content">
                    <form class="rv-form form-empty-trash">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fab fa-windows"></i> {{ trans('core/media::media.empty_trash_title') }}
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">

                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{ trans('core/media::media.empty_trash_description') }}</p>
                            <div class="modal-notice"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-danger">{{ trans('core/media::media.confirm') }}</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ trans('core/media::media.close') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_download_url">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" data-downloading="{{trans('core/media::media.downloading')}}" data-text="{{ trans('core/media::media.download_link') }}">
                            <i class="fas fa-cloud-download-alt"></i> {{ trans('core/media::media.download_link') }}
                        </h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}">

                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="rv-form form-download-url">
                            <div id="download-form-wrapper">
                                <div class="form-group mb-3">
                                <textarea rows="4"
                                          name="urls"
                                          class="form-control"
                                          placeholder="http://example.com/image1.jpg&#10;http://example.com/image2.jpg&#10;http://example.com/image3.jpg&#10;..."></textarea>
                                </div>
                                {!! Form::helper(trans('core/media::media.download_explain')) !!}
                            </div>
                            <button class="btn btn-success w-100" type="submit">{{ trans('core/media::media.download_link') }}</button>
                        </form>
                        <div class="modal-notice mt-2" id="modal-notice" style="max-height: 350px;overflow: auto"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" tabindex="-1" role="dialog" id="modal_crop_image">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form class="rv-form form-crop">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                <i class="fas fa-crop"></i> {{ trans('core/media::media.crop') }}
                            </h4>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ trans('core/media::media.close') }}"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="image_id">
                            <input type="hidden" name="crop_data">
                            <div class="row">
                                <div class="col-lg-9">
                                    <div class="crop-image"></div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="mt-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ trans('core/media::media.cropper.height') }}</label>
                                            <input type="text" class="form-control" id="dataHeight">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">{{ trans('core/media::media.cropper.width') }}</label>
                                            <input type="text" class="form-control" id="dataWidth">
                                        </div>
                                        <div class="form-group">
                                            <input type="checkbox" class="custom-control-input" id="aspectRatio">
                                            <label for="aspectRatio">{{ trans('core/media::media.cropper.aspect_ratio') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ trans('core/media::media.close') }}</button>
                            <button type="submit" class="btn btn-primary">{{ trans('core/media::media.crop') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <button class="hidden js-rv-clipboard-temp"></button>
</div>
<script type="text/x-custom-template" id="rv_media_loading">
    <div class="loading-wrapper">
        <div class="showbox">
            <div class="loader">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2"
                            stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
    </div>
</script>

<script type="text/x-custom-template" id="rv_action_item">
    <li>
        <a href="javascript:;" class="js-files-action" data-action="__action__">
            <i class="__icon__"></i> __name__
        </a>
    </li>
</script>

<script type="text/x-custom-template" id="rv_media_items_list">
    <div class="rv-media-list">
        <ul>
            <li class="no-items">
                <i class="fas fa-cloud-upload-alt"></i>
                <h3>Drop files and folders here</h3>
                <p>Or use the upload button above.</p>
            </li>
            <li class="rv-media-list-title up-one-level js-up-one-level" title="{{ trans('core/media::media.up_level') }}">
                <div class="custom-checkbox"></div>
                <div class="rv-media-file-name">
                    <i class="fas fa-level-up-alt"></i>
                    <span>...</span>
                </div>
                <div class="rv-media-file-size"></div>
                <div class="rv-media-created-at"></div>
            </li>
        </ul>
    </div>
</script>

<script type="text/x-custom-template" id="rv_media_items_tiles" class="hidden">
    <div class="rv-media-grid">
        <ul>
            <li class="no-items">
                <i class="__noItemIcon__"></i>
                <h3>__noItemTitle__</h3>
                <p>__noItemMessage__</p>
            </li>
            <li class="rv-media-list-title up-one-level js-up-one-level">
                <div class="rv-media-item" data-context="__type__" title="{{ trans('core/media::media.up_level') }}">
                    <div class="rv-media-thumbnail">
                        <i class="fas fa-level-up-alt"></i>
                    </div>
                    <div class="rv-media-description">
                        <div class="title">...</div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</script>

<script type="text/x-custom-template" id="rv_media_items_list_element">
    <li class="rv-media-list-title js-media-list-title js-context-menu" data-context="__type__" title="__name__" data-id="__id__">
        <div class="custom-checkbox">
            <label>
                <input type="checkbox">
                <span></span>
            </label>
        </div>
        <div class="rv-media-file-name">
            __thumb__
            <span>__name__</span>
        </div>
        <div class="rv-media-file-size">__size__</div>
        <div class="rv-media-created-at">__date__</div>
    </li>
</script>

<script type="text/x-custom-template" id="rv_media_items_tiles_element">
    <li class="rv-media-list-title js-media-list-title js-context-menu" data-context="__type__" data-id="__id__">
        <input type="checkbox" class="hidden">
        <div class="rv-media-item" title="__name__">
            <span class="media-item-selected">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M186.301 339.893L96 249.461l-32 30.507L186.301 402 448 140.506 416 110z"></path>
                </svg>
            </span>
            <div class="rv-media-thumbnail">
                __thumb__
            </div>
            <div class="rv-media-description">
                <div class="title title{{ BaseHelper::stringify(request()->input('file_id')) }}">__name__</div>
            </div>
        </div>
    </li>
</script>

<script type="text/x-custom-template" id="rv_media_upload_progress_item">
    <li>
        <div class="rv-table-col">
            <span class="file-name">__fileName__</span>
            <div class="file-error"></div>
        </div>
        <div class="rv-table-col">
            <span class="file-size">__fileSize__</span>
        </div>
        <div class="rv-table-col">
            <span class="label label-__status__">__message__</span>
        </div>
    </li>
</script>

<script type="text/x-custom-template" id="rv_media_breadcrumb_item">
    <li>
        <a href="#" data-folder="__folderId__" class="js-change-folder">__icon__ __name__</a>
    </li>
</script>

<script type="text/x-custom-template" id="rv_media_rename_item">
    <div class="form-group mb-3">
        <div class="input-group">
            <div class="input-group-text">
                <i class="__icon__"></i>
            </div>
            <input class="form-control" placeholder="__placeholder__" value="__value__">
        </div>
    </div>
</script>

<script type="text/x-custom-template" id="rv_media_alt_text_item">
    <div class="form-group mb-3">
        <div class="input-group">
            <div class="input-group-text">
                <i class="__icon__"></i>
            </div>
            <input class="form-control" placeholder="__placeholder__" value="__value__">
        </div>
    </div>
</script>

<script type="text/x-custom-template" id="rv_media_crop_image">
    <img src="__src__" style="display: block;max-width: 100%">
</script>

<div class="media-download-popup">
    <div class="alert alert-success">
        <i class="fas fa-circle-notch fa-spin me-2"></i>
        {{ trans('core/media::media.prepare_file_to_download') }}
    </div>
</div>
