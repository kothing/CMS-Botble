import { RecentItems } from '../Config/MediaConfig'
import { Helpers } from '../Helpers/Helpers'
import { MessageService } from './MessageService'
import Cropper from 'cropperjs'

export class ActionsService {
    static handleDropdown() {
        let selected = Helpers.size(Helpers.getSelectedItems())

        ActionsService.renderActions()

        if (selected > 0) {
            $('.rv-dropdown-actions').removeClass('disabled')
        } else {
            $('.rv-dropdown-actions').addClass('disabled')
        }
    }

    static handlePreview() {
        let selected = []

        Helpers.each(Helpers.getSelectedFiles(), (value) => {
            if (value.preview_url) {
                selected.push({
                    src: value.preview_url,
                    type: value.preview_type,
                })
                RecentItems.push(value.id)
            }
        })

        if (Helpers.size(selected) > 0) {
            $.fancybox.open(selected)
            Helpers.storeRecentItems()
        } else {
            this.handleGlobalAction('download')
        }
    }

    static renderCropImage() {
        const html = $('#rv_media_crop_image').html()
        const modal = $('#modal_crop_image .crop-image').empty()
        const item = Helpers.getSelectedItems()[0]
        const form = $('#modal_crop_image .form-crop')
        let cropData

        const el = html.replace(/__src__/gi, item.full_url)
        modal.append(el)

        const image = modal.find('img')[0]

        const options = {
            minContainerWidth: 550,
            minContainerHeight: 550,
            dragMode: 'move',
            crop(event) {
                cropData = event.detail
                form.find('input[name="image_id"]').val(item.id)
                form.find('input[name="crop_data"]').val(JSON.stringify(cropData))
                setHeight(cropData.height)
                setWidth(cropData.width)
            },
        }
        let cropper = new Cropper(image, options)

        form.find('#aspectRatio').on('click', function () {
            cropper.destroy()
            if ($(this).is(':checked')) {
                options.aspectRatio = cropData.width / cropData.height
            } else {
                options.aspectRatio = null
            }
            cropper = new Cropper(image, options)
        })

        form.find('#dataHeight').on('change', function () {
            cropData.height = parseFloat($(this).val())
            cropper.setData(cropData)
            setHeight(cropData.height)
        })

        form.find('#dataWidth').on('change', function () {
            cropData.width = parseFloat($(this).val())
            cropper.setData(cropData)
            setWidth(cropData.width)
        })

        const setHeight = (height) => {
            form.find('#dataHeight').val(parseInt(height))
        }

        const setWidth = (width) => {
            form.find('#dataWidth').val(parseInt(width))
        }
    }

    static handleCopyLink() {
        let links = ''
        Helpers.each(Helpers.getSelectedFiles(), (value) => {
            if (! Helpers.isEmpty(links)) {
                links += '\n'
            }
            links += value.full_url
        })
        let $clipboardTemp = $('.js-rv-clipboard-temp')
        $clipboardTemp.data('clipboard-text', links)
        new Clipboard('.js-rv-clipboard-temp', {
            text: () => {
                return links
            },
        })
        MessageService.showMessage(
            'success',
            Helpers.trans('clipboard.success'),
            Helpers.trans('message.success_header')
        )
        $clipboardTemp.trigger('click')
    }

    static handleGlobalAction(type, callback) {
        let selected = []
        Helpers.each(Helpers.getSelectedItems(), (value) => {
            selected.push({
                is_folder: value.is_folder,
                id: value.id,
                full_url: value.full_url,
            })
        })

        switch (type) {
            case 'rename':
                $('#modal_rename_items').modal('show').find('form.rv-form').data('action', type)
                break
            case 'copy_link':
                ActionsService.handleCopyLink()
                break
            case 'preview':
                ActionsService.handlePreview()
                break
            case 'alt_text':
                $('#modal_alt_text_items').modal('show').find('form.rv-form').data('action', type)
                break
            case 'crop':
                $('#modal_crop_image').modal('show').find('form.rv-form').data('action', type)
                break
            case 'trash':
                $('#modal_trash_items').modal('show').find('form.rv-form').data('action', type)
                break
            case 'delete':
                $('#modal_delete_items').modal('show').find('form.rv-form').data('action', type)
                break
            case 'empty_trash':
                $('#modal_empty_trash').modal('show').find('form.rv-form').data('action', type)
                break
            case 'download':
                let files = []
                Helpers.each(Helpers.getSelectedItems(), (value) => {
                    if (!Helpers.inArray(Helpers.getConfigs().denied_download, value.mime_type)) {
                        files.push({
                            id: value.id,
                            is_folder: value.is_folder,
                        })
                    }
                })

                if (files.length) {
                    ActionsService.handleDownload(files)
                } else {
                    MessageService.showMessage(
                        'error',
                        Helpers.trans('download.error'),
                        Helpers.trans('message.error_header')
                    )
                }
                break
            default:
                ActionsService.processAction(
                    {
                        selected: selected,
                        action: type,
                    },
                    callback
                )
                break
        }
    }

    static processAction(data, callback = null) {
        $.ajax({
            url: RV_MEDIA_URL.global_actions,
            type: 'POST',
            data: data,
            dataType: 'json',
            beforeSend: () => {
                Helpers.showAjaxLoading()
            },
            success: (res) => {
                Helpers.resetPagination()
                if (!res.error) {
                    MessageService.showMessage(
                        'success',
                        res.message,
                        Helpers.trans('message.success_header')
                    )
                } else {
                    MessageService.showMessage('error', res.message, Helpers.trans('message.error_header'))
                }
                if (callback) {
                    callback(res)
                }
            },
            complete: () => {
                Helpers.hideAjaxLoading()
            },
            error: (data) => {
                MessageService.handleError(data)
            },
        })
    }

    static renderRenameItems() {
        let VIEW = $('#rv_media_rename_item').html()
        let $itemsWrapper = $('#modal_rename_items .rename-items').empty()

        Helpers.each(Helpers.getSelectedItems(), (value) => {
            let item = VIEW.replace(/__icon__/gi, value.icon || 'fa fa-file')
                .replace(/__placeholder__/gi, 'Input file name')
                .replace(/__value__/gi, value.name)

            let $item = $(item)
            $item.data('id', value.id)
            $item.data('is_folder', value.is_folder)
            $item.data('name', value.name)
            $itemsWrapper.append($item)
        })
    }

    static renderAltTextItems() {
        let VIEW = $('#rv_media_alt_text_item').html()
        let $itemsWrapper = $('#modal_alt_text_items .alt-text-items').empty()

        Helpers.each(Helpers.getSelectedItems(), (value) => {
            let item = VIEW.replace(/__icon__/gi, value.icon || 'fa fa-file')
                .replace(/__placeholder__/gi, 'Input file alt')
                .replace(/__value__/gi, value.alt === null ? '' : value.alt)

            let $item = $(item)
            $item.data('id', value.id)
            $item.data('alt', value.alt)
            $itemsWrapper.append($item)
        })
    }

    static renderActions() {
        let hasFolderSelected = Helpers.getSelectedFolder().length > 0

        let ACTION_TEMPLATE = $('#rv_action_item').html()
        let initializedItem = 0
        let $dropdownActions = $('.rv-dropdown-actions .dropdown-menu')
        $dropdownActions.empty()

        let actionsList = $.extend({}, true, Helpers.getConfigs().actions_list)

        if (hasFolderSelected) {
            actionsList.basic = Helpers.arrayReject(actionsList.basic, (item) => {
                return item.action === 'preview'
            })
            actionsList.basic = Helpers.arrayReject(actionsList.basic, (item) => {
                return item.action === 'crop'
            })
            actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                return item.action === 'alt_text'
            })
            actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                return item.action === 'copy_link'
            })

            if (! Helpers.hasPermission('folders.create')) {
                actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                    return item.action === 'make_copy'
                })
            }

            if (! Helpers.hasPermission('folders.edit')) {
                actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                    return Helpers.inArray(['rename'], item.action)
                })

                actionsList.user = Helpers.arrayReject(actionsList.user, (item) => {
                    return Helpers.inArray(['rename'], item.action)
                })
            }

            if (! Helpers.hasPermission('folders.trash')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['trash', 'restore'], item.action)
                })
            }

            if (! Helpers.hasPermission('folders.destroy')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['delete'], item.action)
                })
            }

            if (!Helpers.hasPermission('folders.favorite')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['favorite', 'remove_favorite'], item.action)
                })
            }
        }

        let selectedFiles = Helpers.getSelectedFiles()

        let canPreview = Helpers.arrayFilter(selectedFiles, function (value) {
            return value.preview_url
        }).length

        if (!canPreview) {
            actionsList.basic = Helpers.arrayReject(actionsList.basic, (item) => {
                return item.action === 'preview'
            })
        }

        let fileIsImage = Helpers.arrayFilter(selectedFiles, function (value) {
            return value.type === 'image'
        }).length

        if (!fileIsImage) {
            actionsList.basic = Helpers.arrayReject(actionsList.basic, (item) => {
                return item.action === 'crop'
            })

            actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                return item.action === 'alt_text'
            })
        }

        if (selectedFiles.length > 0) {
            if (!Helpers.hasPermission('files.create')) {
                actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                    return item.action === 'make_copy'
                })
            }

            if (!Helpers.hasPermission('files.edit')) {
                actionsList.file = Helpers.arrayReject(actionsList.file, (item) => {
                    return Helpers.inArray(['rename'], item.action)
                })
            }

            if (!Helpers.hasPermission('files.trash')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['trash', 'restore'], item.action)
                })
            }

            if (!Helpers.hasPermission('files.destroy')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['delete'], item.action)
                })
            }

            if (!Helpers.hasPermission('files.favorite')) {
                actionsList.other = Helpers.arrayReject(actionsList.other, (item) => {
                    return Helpers.inArray(['favorite', 'remove_favorite'], item.action)
                })
            }

            if (selectedFiles.length > 1) {
                actionsList.basic = Helpers.arrayReject(actionsList.basic, (item) => {
                    return item.action === 'crop'
                })
            }
        }

        Helpers.each(actionsList, (action, key) => {
            Helpers.each(action, (item, index) => {
                let is_break = false
                switch (Helpers.getRequestParams().view_in) {
                    case 'all_media':
                        if (Helpers.inArray(['remove_favorite', 'delete', 'restore'], item.action)) {
                            is_break = true
                        }
                        break
                    case 'recent':
                        if (Helpers.inArray(['remove_favorite', 'delete', 'restore', 'make_copy'], item.action)) {
                            is_break = true
                        }
                        break
                    case 'favorites':
                        if (Helpers.inArray(['favorite', 'delete', 'restore', 'make_copy'], item.action)) {
                            is_break = true
                        }
                        break
                    case 'trash':
                        if (!Helpers.inArray(['preview', 'delete', 'restore', 'rename', 'download'], item.action)) {
                            is_break = true
                        }
                        break
                }
                if (!is_break) {
                    let template = ACTION_TEMPLATE.replace(/__action__/gi, item.action || '')
                        .replace(/__icon__/gi, item.icon || '')
                        .replace(/__name__/gi, Helpers.trans('actions_list.' + key + '.' + item.action) || item.name)
                    if (!index && initializedItem) {
                        template = '<li role="separator" class="divider"></li>' + template
                    }
                    $dropdownActions.append(template)
                }
            })

            if (action.length > 0) {
                initializedItem++
            }
        })
    }

    static handleDownload(files) {
        const html = $('.media-download-popup')
        let downloadTimeout = null
        $.ajax({
            url: RV_MEDIA_URL.download,
            method: 'POST',
            data: { selected: files },
            xhrFields: {
                responseType: 'blob',
            },
            beforeSend: () => {
                downloadTimeout = setTimeout(() => {
                    html.show()
                }, 1000)
            },
            success: (response, status, xhr) => {
                if (response.error) {
                    MessageService.showMessage('error', response.message)

                    return
                }

                const downloadUrl = URL.createObjectURL(response)
                const a = document.createElement('a')
                const fileName = xhr.getResponseHeader('Content-Disposition').split('filename=')[1].split(';')[0]
                a.href = downloadUrl
                a.download = fileName
                document.body.appendChild(a)
                a.click()
                a.remove()
                window.URL.revokeObjectURL(downloadUrl)
            },
            complete: () => {
                html.hide()
                clearTimeout(downloadTimeout)
            },
            error: (data) => {
                MessageService.handleError(data)
            },
        })
    }
}
