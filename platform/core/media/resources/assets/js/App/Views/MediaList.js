import { Helpers } from '../Helpers/Helpers'
import { ActionsService } from '../Services/ActionsService'

export class MediaList {
    constructor() {
        this.group = {}
        this.group.list = $('#rv_media_items_list').html()
        this.group.tiles = $('#rv_media_items_tiles').html()

        this.item = {}
        this.item.list = $('#rv_media_items_list_element').html()
        this.item.tiles = $('#rv_media_items_tiles_element').html()

        this.$groupContainer = $('.rv-media-items')
    }

    renderData(data, reload = false, load_more_file = false) {
        let _self = this
        let MediaConfig = Helpers.getConfigs()
        let template = _self.group[Helpers.getRequestParams().view_type]

        let view_in = Helpers.getRequestParams().view_in

        if (!Helpers.inArray(['all_media', 'public', 'trash', 'favorites', 'recent'], view_in)) {
            view_in = 'all_media'
        }

        template = template
            .replace(/__noItemIcon__/gi, Helpers.trans('no_item.' + view_in +'.icon') || '')
            .replace(/__noItemTitle__/gi, Helpers.trans('no_item.' + view_in +'.title') || '')
            .replace(/__noItemMessage__/gi, Helpers.trans('no_item.' + view_in +'.message') || '')

        let $result = $(template)
        let $itemsWrapper = $result.find('ul')

        if (load_more_file && this.$groupContainer.find('.rv-media-grid ul').length > 0) {
            $itemsWrapper = this.$groupContainer.find('.rv-media-grid ul')
        }

        if (Helpers.size(data.folders) > 0 || Helpers.size(data.files) > 0 || load_more_file) {
            $('.rv-media-items').addClass('has-items')
        } else {
            $('.rv-media-items').removeClass('has-items')
        }

        Helpers.forEach(data.folders, (value) => {
            let item = _self.item[Helpers.getRequestParams().view_type]
            item = item
                .replace(/__type__/gi, 'folder')
                .replace(/__id__/gi, value.id)
                .replace(/__name__/gi, value.name || '')
                .replace(/__size__/gi, '')
                .replace(/__date__/gi, value.created_at || '')
                .replace(/__thumb__/gi, '<i class="fa fa-folder"></i>')
            let $item = $(item)
            Helpers.forEach(value, (val, index) => {
                $item.data(index, val)
            })
            $item.data('is_folder', true)
            $item.data('icon', MediaConfig.icons.folder)
            $itemsWrapper.append($item)
        })

        Helpers.forEach(data.files, (value) => {
            let item = _self.item[Helpers.getRequestParams().view_type]
            item = item
                .replace(/__type__/gi, 'file')
                .replace(/__id__/gi, value.id)
                .replace(/__name__/gi, value.name || '')
                .replace(/__size__/gi, value.size || '')
                .replace(/__date__/gi, value.created_at || '')
            if (Helpers.getRequestParams().view_type === 'list') {
                item = item.replace(/__thumb__/gi, '<i class="' + value.icon + '"></i>')
            } else {
                item = item.replace(
                    /__thumb__/gi,
                    value.type === 'image'
                        ? '<img src="' + (value.thumb ? value.thumb : value.full_url) + '" alt="' + value.name + '">'
                        : '<i class="' + value.icon + '"></i>'
                )
            }
            let $item = $(item)
            $item.data('is_folder', false)
            Helpers.forEach(value, (val, index) => {
                $item.data(index, val)
            })
            $itemsWrapper.append($item)
        })
        if (reload !== false) {
            _self.$groupContainer.empty()
        }

        if (!(load_more_file && this.$groupContainer.find('.rv-media-grid ul').length > 0)) {
            _self.$groupContainer.append($result)
        }
        _self.$groupContainer.find('.loading-wrapper').remove()
        ActionsService.handleDropdown()

        // Trigger event click for file selected
        $('.js-media-list-title[data-id=' + data.selected_file_id + ']').trigger('click')
    }
}
