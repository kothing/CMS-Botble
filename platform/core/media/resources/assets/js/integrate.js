import { Helpers } from './App/Helpers/Helpers'
import { MediaConfig } from './App/Config/MediaConfig'
import { ContextMenuService } from './App/Services/ContextMenuService'

export class EditorService {
    static editorSelectFile(selectedFiles) {
        let is_ckeditor = Helpers.getUrlParam('CKEditor') || Helpers.getUrlParam('CKEditorFuncNum')

        if (window.opener && is_ckeditor) {
            let firstItem = Helpers.arrayFirst(selectedFiles)

            window.opener.CKEDITOR.tools.callFunction(Helpers.getUrlParam('CKEditorFuncNum'), firstItem.full_url)

            if (window.opener) {
                window.close()
            }
        } else {
            // No WYSIWYG editor found, use custom method.
        }
    }
}

class rvMedia {
    constructor(selector, options) {
        window.rvMedia = window.rvMedia || {}

        let $body = $('body')

        let defaultOptions = {
            multiple: true,
            type: '*',
            onSelectFiles: (files, $el) => {},
        }

        options = $.extend(true, defaultOptions, options)

        let clickCallback = (event) => {
            event.preventDefault()
            let $current = $(event.currentTarget)

            $('#rv_media_modal').modal('show')

            window.rvMedia.options = options
            window.rvMedia.options.open_in = 'modal'

            window.rvMedia.$el = $current

            MediaConfig.request_params.filter = 'everything'
            Helpers.storeConfig()

            let elementOptions = window.rvMedia.$el.data('rv-media')
            if (typeof elementOptions !== 'undefined' && elementOptions.length > 0) {
                elementOptions = elementOptions[0]
                window.rvMedia.options = $.extend(true, window.rvMedia.options, elementOptions || {})
                if (typeof elementOptions.selected_file_id !== 'undefined') {
                    window.rvMedia.options.is_popup = true
                } else if (typeof window.rvMedia.options.is_popup !== 'undefined') {
                    window.rvMedia.options.is_popup = undefined
                }
            }

            if ($('#rv_media_body .rv-media-container').length === 0) {
                $('#rv_media_body').load(RV_MEDIA_URL.popup, (data) => {
                    if (data.error) {
                        alert(data.message)
                    }

                    $('#rv_media_body')
                        .removeClass('media-modal-loading')
                        .closest('.modal-content')
                        .removeClass('bb-loading')
                    $(document).find('.rv-media-container .js-change-action[data-type=refresh]').trigger('click')

                    if (Helpers.getRequestParams().filter !== 'everything') {
                        $('.rv-media-actions .btn.js-rv-media-change-filter-group.js-filter-by-type').hide()
                    }

                    ContextMenuService.destroyContext()
                    ContextMenuService.initContext()
                })
            } else {
                $(document).find('.rv-media-container .js-change-action[data-type=refresh]').trigger('click')
            }
        }

        if (typeof selector === 'string') {
            $body.off('click', selector).on('click', selector, clickCallback)
        } else {
            selector.off('click').on('click', clickCallback)
        }
    }
}

window.RvMediaStandAlone = rvMedia

$('.js-insert-to-editor')
    .off('click')
    .on('click', function (event) {
        event.preventDefault()
        let selectedFiles = Helpers.getSelectedFiles()
        if (Helpers.size(selectedFiles) > 0) {
            EditorService.editorSelectFile(selectedFiles)
        }
    })

$.fn.rvMedia = function (options) {
    let $selector = $(this)

    MediaConfig.request_params.filter = 'everything'
    $(document)
        .find('.js-insert-to-editor')
        .prop('disabled', MediaConfig.request_params.view_in === 'trash')
    Helpers.storeConfig()

    new rvMedia($selector, options)
}
