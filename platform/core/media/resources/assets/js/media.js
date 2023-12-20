import { MediaConfig } from './App/Config/MediaConfig'
import { Helpers } from './App/Helpers/Helpers'
import { MediaService } from './App/Services/MediaService'
import { FolderService } from './App/Services/FolderService'
import { UploadService } from './App/Services/UploadService'
import { ActionsService } from './App/Services/ActionsService'
import { DownloadService } from './App/Services/DownloadService'
import { EditorService } from './integrate'

class MediaManagement {
    constructor() {
        this.MediaService = new MediaService()
        this.UploadService = new UploadService()
        this.FolderService = new FolderService()
        this.DownloadService = new DownloadService()

        this.$body = $('body')
    }

    init() {
        Helpers.resetPagination()
        this.setupLayout()

        this.handleMediaList()
        this.changeViewType()
        this.changeFilter()
        this.search()
        this.handleActions()

        this.UploadService.init()

        this.handleModals()
        this.scrollGetMore()
    }

    setupLayout() {
        /**
         * Sidebar
         */
        let $current_filter = $(
            '.js-rv-media-change-filter[data-type="filter"][data-value="' + Helpers.getRequestParams().filter + '"]'
        )

        $current_filter
            .closest('li')
            .addClass('active')
            .closest('.dropdown')
            .find('.js-rv-media-filter-current')
            .html('(' + $current_filter.html() + ')')

        let $current_view_in = $(
            '.js-rv-media-change-filter[data-type="view_in"][data-value="' + Helpers.getRequestParams().view_in + '"]'
        )

        $current_view_in
            .closest('li')
            .addClass('active')
            .closest('.dropdown')
            .find('.js-rv-media-filter-current')
            .html('(' + $current_view_in.html() + ')')

        if (Helpers.isUseInModal()) {
            $('.rv-media-footer').removeClass('hidden')
        }

        /**
         * Sort
         */
        $('.js-rv-media-change-filter[data-type="sort_by"][data-value="' + Helpers.getRequestParams().sort_by + '"]')
            .closest('li')
            .addClass('active')

        /**
         * Details pane
         */
        let $mediaDetailsCheckbox = $('#media_details_collapse')
        $mediaDetailsCheckbox.prop('checked', MediaConfig.hide_details_pane || false)

        setTimeout(() => {
            $('.rv-media-details').removeClass('hidden')
        }, 300)

        $mediaDetailsCheckbox.on('change', (event) => {
            event.preventDefault()
            MediaConfig.hide_details_pane = $(event.currentTarget).is(':checked')
            Helpers.storeConfig()
        })

        $(document).on('click', '.js-download-action', (event) => {
            event.preventDefault()
            $('#modal_download_url').modal('show')
        })

        $(document).on('click', '.js-create-folder-action', (event) => {
            event.preventDefault()
            $('#modal_add_folder').modal('show')
        })
    }

    handleMediaList() {
        let _self = this

        /*Ctrl key in Windows*/
        let ctrl_key = false

        /*Command key in MAC*/
        let meta_key = false

        /*Shift key*/
        let shift_key = false

        $(document).on('keyup keydown', (e) => {
            /*User hold ctrl key*/
            ctrl_key = e.ctrlKey
            /*User hold command key*/
            meta_key = e.metaKey
            /*User hold shift key*/
            shift_key = e.shiftKey
        })

        _self.$body
            .off('click', '.js-media-list-title')
            .on('click', '.js-media-list-title', (event) => {
                event.preventDefault()
                let $current = $(event.currentTarget)

                if (shift_key) {
                    let firstItem = Helpers.arrayFirst(Helpers.getSelectedItems())
                    if (firstItem) {
                        let firstIndex = firstItem.index_key
                        let currentIndex = $current.index()
                        $('.rv-media-items li').each((index, el) => {
                            if (index > firstIndex && index <= currentIndex) {
                                $(el).find('input[type=checkbox]').prop('checked', true)
                            }
                        })
                    }
                } else if (!ctrl_key && !meta_key) {
                    $current.closest('.rv-media-items').find('input[type=checkbox]').prop('checked', false)
                }

                let $lineCheckBox = $current.find('input[type=checkbox]')
                $lineCheckBox.prop('checked', true)
                ActionsService.handleDropdown()

                _self.MediaService.getFileDetails($current.data())
            })
            .on('dblclick', '.js-media-list-title', (event) => {
                event.preventDefault()

                let data = $(event.currentTarget).data()
                if (data.is_folder === true) {
                    Helpers.resetPagination()
                    _self.FolderService.changeFolder(data.id)
                } else {
                    if (!Helpers.isUseInModal()) {
                        ActionsService.handlePreview()
                    } else if (Helpers.getConfigs().request_params.view_in !== 'trash') {
                        let selectedFiles = Helpers.getSelectedFiles()
                        if (Helpers.size(selectedFiles) > 0) {
                            EditorService.editorSelectFile(selectedFiles)
                        }
                    }
                }
            })
            .on('dblclick', '.js-up-one-level', (event) => {
                event.preventDefault()
                let count = $('.rv-media-breadcrumb .breadcrumb li').length
                $('.rv-media-breadcrumb .breadcrumb li:nth-child(' + (count - 1) + ') a').trigger('click')
            })
            .on('contextmenu', '.js-context-menu', (event) => {
                if (!$(event.currentTarget).find('input[type=checkbox]').is(':checked')) {
                    $(event.currentTarget).trigger('click')
                }
            })
            .on('click contextmenu', '.rv-media-items', (e) => {
                if (!Helpers.size(e.target.closest('.js-context-menu'))) {
                    $('.rv-media-items input[type="checkbox"]').prop('checked', false)
                    $('.rv-dropdown-actions').addClass('disabled')
                    _self.MediaService.getFileDetails({
                        icon: 'far fa-image',
                        nothing_selected: '',
                    })
                }
            })
    }

    changeViewType() {
        let _self = this
        _self.$body
            .off('click', '.js-rv-media-change-view-type .btn')
            .on('click', '.js-rv-media-change-view-type .btn', (event) => {
                event.preventDefault()
                let $current = $(event.currentTarget)
                if ($current.hasClass('active')) {
                    return
                }
                $current.closest('.js-rv-media-change-view-type').find('.btn').removeClass('active')
                $current.addClass('active')

                MediaConfig.request_params.view_type = $current.data('type')

                if ($current.data('type') === 'trash') {
                    $(document).find('.js-insert-to-editor').prop('disabled', true)
                } else {
                    $(document).find('.js-insert-to-editor').prop('disabled', false)
                }

                Helpers.storeConfig()

                if (typeof RV_MEDIA_CONFIG.pagination != 'undefined') {
                    if (typeof RV_MEDIA_CONFIG.pagination.paged != 'undefined') {
                        RV_MEDIA_CONFIG.pagination.paged = 1
                    }
                }

                _self.MediaService.getMedia(true, false)
            })
        $('.js-rv-media-change-view-type .btn[data-type="' + Helpers.getRequestParams().view_type + '"]').trigger(
            'click'
        )

        this.bindIntegrateModalEvents()
    }

    changeFilter() {
        let _self = this
        _self.$body.off('click', '.js-rv-media-change-filter').on('click', '.js-rv-media-change-filter', (event) => {
            event.preventDefault()
            if (!Helpers.isOnAjaxLoading()) {
                let $current = $(event.currentTarget)
                let $parent = $current.closest('ul')
                let data = $current.data()

                MediaConfig.request_params[data.type] = data.value

                if (window.rvMedia.options) {
                    window.rvMedia.options.view_in = data.value
                }

                if (data.type === 'view_in') {
                    MediaConfig.request_params.folder_id = 0
                    if (data.value === 'trash') {
                        $(document).find('.js-insert-to-editor').prop('disabled', true)
                    } else {
                        $(document).find('.js-insert-to-editor').prop('disabled', false)
                    }
                }

                $current
                    .closest('.dropdown')
                    .find('.js-rv-media-filter-current')
                    .html('(' + $current.html() + ')')

                Helpers.storeConfig()
                MediaService.refreshFilter()

                Helpers.resetPagination()
                _self.MediaService.getMedia(true)

                $parent.find('> li').removeClass('active')
                $current.closest('li').addClass('active')
            }
        })
    }

    search() {
        let _self = this
        $('.input-search-wrapper input[type="text"]').val(Helpers.getRequestParams().search || '')
        _self.$body.off('submit', '.input-search-wrapper').on('submit', '.input-search-wrapper', (event) => {
            event.preventDefault()
            MediaConfig.request_params.search = $(event.currentTarget).find('input[type="text"]').val()

            Helpers.storeConfig()
            Helpers.resetPagination()
            _self.MediaService.getMedia(true)
        })
    }

    handleActions() {
        let _self = this

        _self.$body
            .off('click', '.rv-media-actions .js-change-action[data-type="refresh"]')
            .on('click', '.rv-media-actions .js-change-action[data-type="refresh"]', (event) => {
                event.preventDefault()

                Helpers.resetPagination()

                let ele_options =
                    typeof window.rvMedia.$el !== 'undefined' ? window.rvMedia.$el.data('rv-media') : undefined
                if (
                    typeof ele_options !== 'undefined' &&
                    ele_options.length > 0 &&
                    typeof ele_options[0].selected_file_id !== 'undefined'
                ) {
                    _self.MediaService.getMedia(true, true)
                } else {
                    _self.MediaService.getMedia(true, false)
                }
            })
            .off('click', '.rv-media-items li.no-items')
            .on('click', '.rv-media-items li.no-items', (event) => {
                event.preventDefault()
                $('.rv-media-header .rv-media-top-header .rv-media-actions .js-dropzone-upload').trigger('click')
            })
            .off('submit', '.form-add-folder')
            .on('submit', '.form-add-folder', (event) => {
                event.preventDefault()
                let $input = $(event.currentTarget).find('input[type=text]')
                let folderName = $input.val()
                _self.FolderService.create(folderName)
                $input.val('')
                return false
            })
            .off('click', '.js-change-folder')
            .on('click', '.js-change-folder', (event) => {
                event.preventDefault()
                let folderId = $(event.currentTarget).data('folder')
                Helpers.resetPagination()
                _self.FolderService.changeFolder(folderId)
            })
            .off('click', '.js-files-action')
            .on('click', '.js-files-action', (event) => {
                event.preventDefault()
                ActionsService.handleGlobalAction($(event.currentTarget).data('action'), () => {
                    Helpers.resetPagination()
                    _self.MediaService.getMedia(true)
                })
            })
            .off('submit', '.form-download-url')
            .on('submit', '.form-download-url', async (event) => {
                let $el = $('#modal_download_url'),
                    $wrapper = $el.find('#download-form-wrapper'),
                    $notice = $el.find('#modal-notice').empty()
                event.preventDefault()
                let $header = $el.find('.modal-title')
                let $input = $el.find('textarea[name="urls"]').prop('disabled', true)
                let $button = $el.find('[type="submit"]').addClass('button-loading').prop('disabled', true)

                let url = $input.val()
                let remainUrls = []
                $wrapper.slideUp()

                // start to download
                await _self.DownloadService.download(url, (progress, item, url) => {
                    let $noticeItem = $(`
                        <div class="p-2 text-primary">
                            <i class="fa fa-info-circle"></i>
                            <span>${item}</span>
                        </div>
                    `)
                    $notice.append($noticeItem).scrollTop($notice[0].scrollHeight)
                    $header.html(
                        `<i class="fas fa-cloud-download-alt"></i> ${$header.data('downloading')} (${progress})`
                    )
                    return (success, message = '') => {
                        if (!success) {
                            remainUrls.push(url)
                        }
                        $noticeItem.find('span').text(`${item}: ${message}`)
                        $noticeItem
                            .attr('class', `p-2 text-${success ? 'success' : 'danger'}`)
                            .find('i')
                            .attr('class', success ? 'fas fa-check-circle' : 'fas fa-times-circle')
                    }
                })

                $wrapper.slideDown()
                $input.val(remainUrls.join('\n')).prop('disabled', false)
                $header.html(`<i class="fas fa-cloud-download-alt"></i> ${$header.data('text')}`)
                $button.removeClass('button-loading').prop('disabled', false)
                return false
            })
    }

    handleModals() {
        let _self = this

        /*Rename files*/
        _self.$body.on('show.bs.modal', '#modal_rename_items', () => {
            ActionsService.renderRenameItems()
        })

        _self.$body.on('show.bs.modal', '#modal_alt_text_items', () => {
            ActionsService.renderAltTextItems()
        })

        _self.$body.on('show.bs.modal', '#modal_crop_image', () => {
            ActionsService.renderCropImage()
        })

        _self.$body.on('hidden.bs.modal', '#modal_download_url', () => {
            let $el = $('#modal_download_url')
            $el.find('textarea').val('')
            $el.find('#modal-notice').empty()
        })

        _self.$body
            .off('submit', '#modal_crop_image .form-crop')
            .on('submit', '#modal_crop_image .form-crop', (event) => {
                event.preventDefault()

                const $form = $(event.currentTarget)
                const imageId = $form.find('input[name="image_id"]').val()
                const cropData = $form.find('input[name="crop_data"]').val()
                ActionsService.processAction(
                    {
                        action: $form.data('action'),
                        imageId,
                        cropData,
                    },
                    (response) => {
                        if (!response.error) {
                            $form.closest('.modal').modal('hide')
                            _self.MediaService.getMedia(true)
                        }
                    }
                )
            })

        _self.$body
            .off('submit', '#modal_rename_items .form-rename')
            .on('submit', '#modal_rename_items .form-rename', (event) => {
                event.preventDefault()
                let items = []
                let $form = $(event.currentTarget)

                $('#modal_rename_items .form-control').each((index, el) => {
                    let $current = $(el)
                    let data = $current.closest('.form-group').data()
                    data.name = $current.val()
                    items.push(data)
                })

                ActionsService.processAction(
                    {
                        action: $form.data('action'),
                        selected: items,
                    },
                    (res) => {
                        if (!res.error) {
                            $form.closest('.modal').modal('hide')
                            _self.MediaService.getMedia(true)
                        } else {
                            $('#modal_rename_items .form-group').each((index, el) => {
                                let $current = $(el)
                                if (Helpers.inArray(res.data, $current.data('id'))) {
                                    $current.addClass('has-error')
                                } else {
                                    $current.removeClass('has-error')
                                }
                            })
                        }
                    }
                )
            })

        _self.$body
            .off('submit', '#modal_alt_text_items .form-alt-text')
            .on('submit', '#modal_alt_text_items .form-alt-text', (event) => {
                event.preventDefault()

                let items = []
                let $form = $(event.currentTarget)

                $('#modal_alt_text_items .form-control').each((index, el) => {
                    let $current = $(el)
                    let data = $current.closest('.form-group').data()
                    data.alt = $current.val()
                    items.push(data)
                })

                ActionsService.processAction(
                    {
                        action: $form.data('action'),
                        selected: items,
                    },
                    (res) => {
                        if (!res.error) {
                            $form.closest('.modal').modal('hide')
                            _self.MediaService.getMedia(true)
                        } else {
                            $('#modal_alt_text_items .form-group').each((index, el) => {
                                let $current = $(el)
                                if (Helpers.inArray(res.data, $current.data('id'))) {
                                    $current.addClass('has-error')
                                } else {
                                    $current.removeClass('has-error')
                                }
                            })
                        }
                    }
                )
            })

        /*Delete files*/
        _self.$body.off('submit', '.form-delete-items').on('submit', '.form-delete-items', (event) => {
            event.preventDefault()
            let items = []
            let $form = $(event.currentTarget)

            Helpers.each(Helpers.getSelectedItems(), (value) => {
                items.push({
                    id: value.id,
                    is_folder: value.is_folder,
                })
            })

            ActionsService.processAction(
                {
                    action: $form.data('action'),
                    selected: items,
                },
                (res) => {
                    $form.closest('.modal').modal('hide')
                    if (!res.error) {
                        _self.MediaService.getMedia(true)
                    }
                }
            )
        })

        /*Empty trash*/
        _self.$body
            .off('submit', '#modal_empty_trash .rv-form')
            .on('submit', '#modal_empty_trash .rv-form', (event) => {
                event.preventDefault()
                let $form = $(event.currentTarget)

                ActionsService.processAction(
                    {
                        action: $form.data('action'),
                    },
                    () => {
                        $form.closest('.modal').modal('hide')
                        _self.MediaService.getMedia(true)
                    }
                )
            })

        if (MediaConfig.request_params.view_in === 'trash') {
            $(document).find('.js-insert-to-editor').prop('disabled', true)
        } else {
            $(document).find('.js-insert-to-editor').prop('disabled', false)
        }

        this.bindIntegrateModalEvents()
    }

    checkFileTypeSelect(selectedFiles) {
        if (typeof window.rvMedia.$el !== 'undefined') {
            let firstItem = Helpers.arrayFirst(selectedFiles)
            let ele_options = window.rvMedia.$el.data('rv-media')
            if (
                typeof ele_options !== 'undefined' &&
                typeof ele_options[0] !== 'undefined' &&
                typeof ele_options[0].file_type !== 'undefined' &&
                firstItem !== 'undefined' &&
                firstItem.type !== 'undefined'
            ) {
                if (!ele_options[0].file_type.match(firstItem.type)) {
                    return false
                } else {
                    if (typeof ele_options[0].ext_allowed !== 'undefined' && $.isArray(ele_options[0].ext_allowed)) {
                        if ($.inArray(firstItem.mime_type, ele_options[0].ext_allowed) === -1) {
                            return false
                        }
                    }
                }
            }
        }
        return true
    }

    bindIntegrateModalEvents() {
        let $mainModal = $('#rv_media_modal')
        let _self = this
        $mainModal.off('click', '.js-insert-to-editor').on('click', '.js-insert-to-editor', (event) => {
            event.preventDefault()
            let selectedFiles = Helpers.getSelectedFiles()
            if (Helpers.size(selectedFiles) > 0) {
                window.rvMedia.options.onSelectFiles(selectedFiles, window.rvMedia.$el)
                if (_self.checkFileTypeSelect(selectedFiles)) {
                    $mainModal.find('.btn-close').trigger('click')
                }
            }
        })

        $mainModal.off('dblclick', '.js-media-list-title').on('dblclick', '.js-media-list-title', (event) => {
            event.preventDefault()
            if (Helpers.getConfigs().request_params.view_in !== 'trash') {
                let selectedFiles = Helpers.getSelectedFiles()
                if (Helpers.size(selectedFiles) > 0) {
                    window.rvMedia.options.onSelectFiles(selectedFiles, window.rvMedia.$el)
                    if (_self.checkFileTypeSelect(selectedFiles)) {
                        $mainModal.find('.btn-close').trigger('click')
                    }
                }
            } else {
                ActionsService.handlePreview()
            }
        })
    }

    static setupSecurity() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
        })
    }

    // Scroll get more media
    scrollGetMore() {
        let _self = this
        $('.rv-media-main .rv-media-items').bind('DOMMouseScroll mousewheel', (e) => {
            if (e.originalEvent.detail > 0 || e.originalEvent.wheelDelta < 0) {
                let loadMore
                if ($(e.currentTarget).closest('.media-modal').length > 0) {
                    loadMore =
                        $(e.currentTarget).scrollTop() + $(e.currentTarget).innerHeight() / 2 >=
                        $(e.currentTarget)[0].scrollHeight - 450
                } else {
                    loadMore =
                        $(e.currentTarget).scrollTop() + $(e.currentTarget).innerHeight() >=
                        $(e.currentTarget)[0].scrollHeight - 150
                }

                if (loadMore) {
                    if (typeof RV_MEDIA_CONFIG.pagination != 'undefined' && RV_MEDIA_CONFIG.pagination.has_more) {
                        _self.MediaService.getMedia(false, false, true)
                    }
                }
            }
        })
    }
}

$(document).ready(() => {
    window.rvMedia = window.rvMedia || {}

    MediaManagement.setupSecurity()
    new MediaManagement().init()
})
